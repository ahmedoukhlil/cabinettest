<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rendezvou;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Acte;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CreateRendezVous extends Component
{
    use WithPagination;

    // Propriétés du formulaire
    public $patient_id = '';
    public $selectedPatient = null;
    public $medecin_id = '';
    public $date_rdv = '';
    public $heure_rdv;
    public $acte_prevu = 'Consultation';
    public $rdv_confirmer = 'En Attente';
    // Suppression de $patient pour éviter les problèmes de type avec Livewire

    // La recherche de patients est maintenant gérée par le composant PatientSearch
    public $medecins = [];
    public $actes = [];

    // Propriétés pour les permissions
    public $canManageRdv = false;
    public $canViewAllRdv = false;
    public $isDocteurProprietaire = false;
    public $isSecretaire = false;
    public $isDocteur = false;
    
    // Propriété pour l'URL d'impression
    public $printUrl = '';
    public $showPrintButton = false;
    
    // Propriété pour empêcher la fermeture du modal
    public $keepModalOpen = true;

    protected $rules = [
        'patient_id' => 'required',
        'medecin_id' => 'required',
        'date_rdv' => 'required|date',
        'heure_rdv' => 'required',
        'acte_prevu' => 'required|min:3',
        'rdv_confirmer' => 'required'
    ];

    protected $messages = [
        'patient_id.required' => 'Le patient est requis',
        'medecin_id.required' => 'Le médecin est requis',
        'date_rdv.required' => 'La date est requise',
        'date_rdv.date' => 'La date n\'est pas valide',
        'heure_rdv.required' => 'L\'heure est requise',
        'acte_prevu.required' => 'L\'acte prévu est requis',
        'acte_prevu.min' => 'L\'acte prévu doit contenir au moins 3 caractères',
        'rdv_confirmer.required' => 'Le statut est requis'
    ];

    protected $listeners = [
        'patientSelected' => 'handlePatientSelected',
        'patientCleared' => 'handlePatientCleared',
    ];

    // Empêcher le rechargement automatique du composant
    protected $queryString = [];

    public function mount($patient = null)
    {
        // Charger seulement les médecins nécessaires
        $this->medecins = Medecin::select('idMedecin', 'Nom')
            ->where('Masquer', 0)
            ->orderBy('Nom')
            ->get();
        // Charger seulement les actes nécessaires
        $this->actes = Acte::select('ID', 'Acte')
            ->where('Masquer', 0)
            ->orderBy('Acte')
            ->limit(20) // Limiter à 20 actes les plus utilisés
            ->get();
        $this->initializePermissions();
        
        // Gérer le patient passé en paramètre
        \Log::info('CreateRendezVous mount: patient reçu', ['patient' => $patient]);
        
        if ($patient) {
            if (is_array($patient)) {
                $this->patient_id = $patient['ID'];
                $this->selectedPatient = $patient;
            } else {
                $this->patient_id = $patient->ID;
                $this->selectedPatient = $patient->toArray();
            }
            
            \Log::info('CreateRendezVous mount: patient traité', [
                'patient_id' => $this->patient_id,
                'selectedPatient' => $this->selectedPatient
            ]);
            
            // Sauvegarder l'état dans la session
            session(['rdv_patient' => [
                'id' => $this->patient_id,
                'data' => $this->selectedPatient
            ]]);
        }
        
        // Initialiser la date et l'heure par défaut
        $this->date_rdv = now()->format('Y-m-d');
        $this->heure_rdv = now()->format('H:i');
    }

    protected function initializePermissions()
    {
        $user = Auth::user();
        
        // Définir les rôles
        $this->isDocteurProprietaire = $user->isDocteurProprietaire();
        $this->isSecretaire = $user->isSecretaire();
        $this->isDocteur = $user->isDocteur() && !$user->isDocteurProprietaire();

        // Définir les permissions
        $this->canManageRdv = $this->isDocteurProprietaire || $this->isSecretaire || $this->isDocteur;
        $this->canViewAllRdv = $this->isDocteurProprietaire || $this->isSecretaire;
        
        // Si c'est un docteur simple, on force son ID
        if ($this->isDocteur) {
            $this->medecin_id = $user->fkidmedecin;
        }
    }

    public function createRendezVous()
    {
        $this->validate();

        // Vérification supplémentaire des permissions
        $user = Auth::user();
        if ($this->isDocteur && $this->medecin_id != $user->fkidmedecin) {
            session()->flash('error', 'Vous ne pouvez créer des rendez-vous que pour vous-même.');
            return;
        }

        // Vérification des conflits d'horaires
        if (Rendezvou::hasConflict($this->medecin_id, $this->date_rdv, $this->heure_rdv)) {
            session()->flash('error', 'Ce créneau horaire n\'est pas disponible. Le médecin a déjà un rendez-vous à cette heure.');
            return;
        }

        try {
            // Utiliser une transaction pour optimiser les performances
            \DB::beginTransaction();
            
            $rendezVous = Rendezvou::create([
                'fkidPatient' => $this->patient_id,
                'fkidMedecin' => $this->medecin_id,
                'dtPrevuRDV' => $this->date_rdv,
                'HeureRdv' => $this->heure_rdv,
                'ActePrevu' => $this->acte_prevu,
                'rdvConfirmer' => $this->rdv_confirmer,
                'DtAjRdv' => now(),
                'user' => Auth::id(),
                'fkidcabinet' => Auth::user()->fkidcabinet,
                'OrdreRDV' => Rendezvou::generateNextOrderNumber($this->date_rdv, $this->medecin_id)
            ]);

            \DB::commit();

            $this->reset(['patient_id', 'heure_rdv', 'acte_prevu']);
            $this->resetPage();
            session()->flash('message', 'Rendez-vous créé avec succès.');
            
            // Émettre un événement pour réinitialiser le composant PatientSearch
            $this->emit('clearPatient');
            
            // Retourner l'URL pour l'impression du reçu dans un nouvel onglet
            $this->printUrl = route('rendez-vous.print', ['id' => $rendezVous->IDRdv]);
            $this->showPrintButton = true;
            $this->dispatchBrowserEvent('open-receipt', ['url' => $this->printUrl]);
        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Erreur lors de la création du rendez-vous: ' . $e->getMessage());
        }
    }

    public function updatedDateRdv($value)
    {
        $this->resetPage();
    }

    public function updatedMedecinId($value)
    {
        $this->resetPage();
    }

    // Suppression de la logique de recherche dupliquée - maintenant gérée par le composant PatientSearch

    public function printRendezVous()
    {
        if ($this->printUrl) {
            $this->dispatchBrowserEvent('open-receipt', ['url' => $this->printUrl]);
        }
    }

    public function handlePatientSelected($patient)
    {
        \Log::info('CreateRendezVous: handlePatientSelected appelé', ['patient' => $patient]);
        
        if ($patient === null) {
            $this->patient_id = null;
            $this->selectedPatient = null;
        } else {
            // Le patient vient du composant PatientSearch sous forme d'array
            $this->patient_id = $patient['ID'];
            $this->selectedPatient = $patient;
        }
        
        // Sauvegarder l'état dans la session
        session(['rdv_patient' => [
            'id' => $this->patient_id,
            'data' => $this->selectedPatient
        ]]);
        
        \Log::info('CreateRendezVous: patient_id mis à jour', ['patient_id' => $this->patient_id]);
        
        // Forcer la mise à jour des propriétés
        $this->emit('refresh');
    }

    public function handlePatientCleared()
    {
        $this->patient_id = null;
        $this->selectedPatient = null;
        session()->forget('rdv_patient');
        
        // Empêcher le re-rendu complet du composant
        $this->skipRender();
    }

    public function getTotalRdvJourProperty()
    {
        return \App\Models\Rendezvou::whereDate('dtPrevuRDV', now()->toDateString())->count();
    }

    // Méthode pour accéder aux propriétés du patient sélectionné de manière sécurisée
    public function getSelectedPatientProperty()
    {
        if ($this->patient_id) {
            return Patient::find($this->patient_id);
        }
        return null;
    }

    public function changerStatutRendezVous($id, $nouveauStatut)
    {
        try {
            $user = Auth::user();
            $rdv = Rendezvou::find($id);

            if (!$this->canManageRdv) {
                session()->flash('error', 'Vous n\'avez pas la permission de modifier des rendez-vous.');
                return;
            }

            // Si c'est un docteur simple, vérifier que le rendez-vous lui appartient
            if ($this->isDocteur && $rdv->fkidMedecin != $user->fkidmedecin) {
                session()->flash('error', 'Vous ne pouvez modifier que vos propres rendez-vous.');
                return;
            }

            $statutsValides = ['En Attente', 'Confirmé', 'En cours', 'Terminé', 'Annulé'];
            
            if (!in_array($nouveauStatut, $statutsValides)) {
                session()->flash('error', 'Statut invalide.');
                return;
            }

            // Utiliser la méthode centralisée du modèle
            $result = Rendezvou::updateStatusWithConflictManagement($id, $nouveauStatut);
            
            if ($result['success']) {
                session()->flash('message', $result['message']);
            } else {
                session()->flash('error', $result['message']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la modification du statut: ' . $e->getMessage());
        }
    }

    public function hydrate()
    {
        // Restaurer l'état du patient depuis la session si nécessaire
        if (!$this->selectedPatient && session()->has('rdv_patient')) {
            $rdvPatient = session('rdv_patient');
            $this->patient_id = $rdvPatient['id'];
            $this->selectedPatient = $rdvPatient['data'];
        }
    }

    public function dehydrate()
    {
        // Sauvegarder l'état du patient dans la session
        if ($this->selectedPatient) {
            session(['rdv_patient' => [
                'id' => $this->patient_id,
                'data' => $this->selectedPatient
            ]]);
        }
    }

    public function render()
    {
        $query = Rendezvou::query()
            ->with(['patient:id,ID,Nom,Prenom', 'medecin:idMedecin,Nom']) // Charger seulement les champs nécessaires
            ->orderBy('dtPrevuRDV', 'desc')
            ->orderBy('HeureRdv', 'desc');

        // Si un médecin est sélectionné, filtrer par ce médecin
        if ($this->medecin_id) {
            $query->where('fkidMedecin', $this->medecin_id);
        }

        // Si une date est sélectionnée, filtrer par cette date
        if ($this->date_rdv) {
            $query->whereDate('dtPrevuRDV', $this->date_rdv);
        }

        // Si c'est un docteur simple, ne montrer que ses rendez-vous
        if ($this->isDocteur) {
            $query->where('fkidMedecin', Auth::user()->fkidmedecin);
        }

        $rendezVous = $query->paginate(8); // Réduire à 8 éléments par page

        return view('livewire.create-rendez-vous', [
            'rendezVous' => $rendezVous,
            'totalRdvJour' => $this->totalRdvJour,
        ])->layout('layouts.app');
    }
}