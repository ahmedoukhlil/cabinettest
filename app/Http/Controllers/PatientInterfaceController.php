<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rendezvou;
use App\Models\Patient;
use App\Models\Consultation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PatientInterfaceController extends Controller
{
    /**
     * Affiche les rendez-vous d'un patient via token
     */
    public function showRendezVous($token)
    {
        try {
            // Décoder le token pour obtenir l'ID du patient
            $patientId = $this->decodeToken($token);
            
            if (!$patientId) {
                return view('patient.error', ['message' => 'Token invalide ou expiré']);
            }

            $patient = Patient::find($patientId);
            
            if (!$patient) {
                return view('patient.error', ['message' => 'Patient non trouvé']);
            }

            // Récupérer tous les rendez-vous du patient
            $rendezVous = Rendezvou::with(['medecin', 'cabinet'])
                ->where('fkidPatient', $patientId)
                ->orderBy('dtPrevuRDV', 'desc')
                ->get();

            // Récupérer le prochain rendez-vous du patient (aujourd'hui ou futur)
            $prochainRdv = Rendezvou::with(['medecin', 'cabinet'])
                ->where('fkidPatient', $patientId)
                ->where('dtPrevuRDV', '>=', now()->format('Y-m-d'))
                ->whereNotIn('rdvConfirmer', ['Annulé', 'annulé'])
                ->orderBy('dtPrevuRDV', 'asc')
                ->orderBy('OrdreRDV', 'asc')
                ->first();

            // Récupérer les rendez-vous du médecin pour la journée (si le patient a un prochain RDV)
            $rendezVousMedecinJournee = collect();
            if ($prochainRdv) {
                $rendezVousMedecinJournee = Rendezvou::with(['patient', 'medecin'])
                    ->where('fkidMedecin', $prochainRdv->fkidMedecin)
                    ->where('dtPrevuRDV', $prochainRdv->dtPrevuRDV)
                    ->orderBy('OrdreRDV', 'asc')
                    ->get();
            }

            $fileAttente = null;
            $positionPatient = null;
            $tempsAttenteEstime = null;
            $patientEnCours = null;
            $positionPatientEnCours = null;

            if ($prochainRdv) {
                // Récupérer tous les rendez-vous du même médecin pour la même date
                $fileAttente = Rendezvou::with(['patient', 'medecin'])
                    ->where('fkidMedecin', $prochainRdv->fkidMedecin)
                    ->where('dtPrevuRDV', $prochainRdv->dtPrevuRDV)
                    ->whereNotIn('rdvConfirmer', ['Annulé', 'annulé'])
                    ->orderBy('OrdreRDV', 'asc')
                    ->get();

                // Utiliser directement l'ordreRDV du patient comme position
                $positionPatient = $prochainRdv->OrdreRDV;
                
                // Estimer le temps d'attente (15 minutes par patient en moyenne)
                $tempsAttenteEstime = $positionPatient * 15; // en minutes

                // Trouver le patient en cours de traitement
                $patientEnCours = $fileAttente->first(function($rdv) {
                    return $rdv->rdvConfirmer == 'En cours';
                });

                if ($patientEnCours) {
                    $positionPatientEnCours = $patientEnCours->OrdreRDV;
                }
            }

            return view('patient.rendez-vous', compact(
                'patient', 
                'rendezVous', 
                'rendezVousMedecinJournee',
                'prochainRdv', 
                'fileAttente', 
                'positionPatient', 
                'tempsAttenteEstime',
                'patientEnCours',
                'positionPatientEnCours'
            ));
            
        } catch (\Exception $e) {
            return view('patient.error', ['message' => 'Erreur lors du chargement des données']);
        }
    }

    /**
     * Affiche les consultations d'un patient via token
     */
    public function showConsultation($token)
    {
        try {
            // Décoder le token pour obtenir l'ID du patient
            $patientId = $this->decodeToken($token);
            
            if (!$patientId) {
                return view('patient.error', ['message' => 'Token invalide ou expiré']);
            }

            $patient = Patient::find($patientId);
            
            if (!$patient) {
                return view('patient.error', ['message' => 'Patient non trouvé']);
            }

            // Récupérer toutes les consultations du patient
            $consultations = Consultation::with(['medecin', 'cabinet'])
                ->where('fkidPatient', $patientId)
                ->orderBy('DateConsultation', 'desc')
                ->get();

            return view('patient.consultation', compact('patient', 'consultations'));
            
        } catch (\Exception $e) {
            return view('patient.error', ['message' => 'Erreur lors du chargement des données']);
        }
    }

    /**
     * Génère un token sécurisé pour un patient
     */
    public static function generateToken($patientId)
    {
        $data = $patientId . '|' . time();
        return base64_encode($data);
    }

    /**
     * Décode un token pour obtenir l'ID du patient
     */
    private function decodeToken($token)
    {
        try {
            $decoded = base64_decode($token);
            $parts = explode('|', $decoded);
            
            if (count($parts) !== 2) {
                return null;
            }

            $patientId = $parts[0];
            $timestamp = $parts[1];

            // Vérifier que le token n'est pas expiré (24 heures)
            if (time() - $timestamp > 24 * 60 * 60) {
                return null;
            }

            return $patientId;
            
        } catch (\Exception $e) {
            return null;
        }
    }
} 