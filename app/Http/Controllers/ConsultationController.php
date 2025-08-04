<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\Acte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    public function create()
    {
        return view('consultations.create');
    }

    public function show($id)
    {
        $facture = Facture::with(['patient', 'details.acte'])->findOrFail($id);
        return view('consultations.show', compact('facture'));
    }

    public function showReceipt($factureId)
    {
        try {
            \Log::info('Tentative d\'accès au reçu de consultation', ['facture_id' => $factureId]);
            
            // Utiliser le cache pour les données du cabinet
            $cabinet = cache()->remember('cabinet_info_' . Auth::id(), 3600, function() {
                $user = Auth::user();
                return [
                    'NomCabinet' => $user->cabinet->NomCabinet ?? 'Cabinet Dentaire',
                    'Adresse' => $user->cabinet->Adresse ?? 'Adresse de Cabinet Dentaire',
                    'Telephone' => $user->cabinet->Telephone ?? 'Téléphone de Cabinet Dentaire'
                ];
            });

            // Précharger toutes les relations nécessaires en une seule requête
            $facture = Facture::with([
                'patient',
                'medecin',
                'details.acte',
                'assureur',
                'rendezVous' => function($query) {
                    $query->select('IDRdv', 'OrdreRDV', 'fkidFacture');
                }
            ])->findOrFail($factureId);

            \Log::info('Facture trouvée', ['facture' => $facture->toArray()]);

            return view('consultations.receipt', compact('facture', 'cabinet'));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage du reçu', [
                'facture_id' => $factureId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function showFacturePatient($factureId)
    {
        // Utiliser le cache pour les données du cabinet (24h car rarement modifiées)
        $cabinet = cache()->remember('cabinet_info_' . Auth::id(), 86400, function() {
            $user = Auth::user();
            return [
                'NomCabinet' => $user->cabinet->NomCabinet ?? 'Cabinet Dentaire',
                'Adresse' => $user->cabinet->Adresse ?? 'Adresse de Cabinet Dentaire',
                'Telephone' => $user->cabinet->Telephone ?? 'Téléphone de Cabinet Dentaire'
            ];
        });

        // Mettre en cache la facture avec ses relations (1h car peut être modifiée)
        $facture = cache()->remember('facture_' . $factureId, 3600, function() use ($factureId) {
            $facture = Facture::where('Idfacture', $factureId)
                ->with([
                    'patient' => function($query) {
                        $query->select('ID', 'IdentifiantPatient', 'NomContact', 'Telephone1', 'IdentifiantAssurance', 'Assureur');
                    },
                    'medecin' => function($query) {
                        $query->select('idMedecin', 'Nom', 'Contact');
                    },
                    'details' => function($query) {
                        $query->select('idDetfacture', 'Actes', 'Quantite', 'PrixFacture', 'fkidfacture')
                              ->orderBy('idDetfacture');
                    },
                    'patient.assureur' => function($query) {
                        $query->select('IDAssureur', 'LibAssurance');
                    }
                ])
                ->first();

            if (!$facture) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
            }

            return $facture;
        });

        // Vider le cache si les données du patient sont manquantes
        if (!$facture->patient) {
            cache()->forget('facture_' . $factureId);
            // Recharger la facture sans cache
            $facture = Facture::with([
                'patient' => function($query) {
                    $query->select('ID', 'IdentifiantPatient', 'NomContact', 'Telephone1', 'IdentifiantAssurance', 'Assureur');
                },
                'medecin' => function($query) {
                    $query->select('idMedecin', 'Nom', 'Contact');
                },
                'details',
                'patient.assureur'
            ])->findOrFail($factureId);
        }

        // Pré-calculer et mettre en cache les montants (1h)
        $montants = cache()->remember('facture_montants_' . $factureId, 3600, function() use ($facture) {
            $restePEC = $facture->TotalPEC - $facture->ReglementPEC;
            $restePatient = $facture->ISTP == 1 
                ? ($facture->TotalfactPatient - $facture->TotReglPatient)
                : ($facture->TotFacture - $facture->TotReglPatient);

            return [
                'restePEC' => $restePEC,
                'restePatient' => $restePatient
            ];
        });

        // Mettre en cache la conversion en lettres (1h)
        $facture->en_lettres = cache()->remember('facture_lettres_' . $factureId, 3600, function() use ($facture) {
            return $this->numberToWords($facture->TotFacture ?? 0);
        });

        // Ajouter les montants calculés à la facture
        $facture->restePEC = $montants['restePEC'];
        $facture->restePatient = $montants['restePatient'];

        // Récupérer l'utilisateur connecté
        $currentUser = Auth::user();

        return view('consultations.facture-patient', compact('facture', 'cabinet', 'currentUser'));
    }

    // Helper simple pour montant en lettres (à remplacer par votre propre logique si besoin)
    private function numberToWords($number)
    {
        // Utilisez un package ou une fonction plus complète si besoin
        $f = new \NumberFormatter("fr", \NumberFormatter::SPELLOUT);
        return ucfirst($f->format($number));
    }
} 