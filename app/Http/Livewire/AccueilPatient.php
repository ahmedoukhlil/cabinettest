<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\Assureur;
use App\Models\RefTypePaiement;
use App\Models\Acte;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\TypeUser;
use App\Models\User;

class AccueilPatient extends Component
{
    use WithPagination;

    // Constantes pour les clés de cache
    const CACHE_TTL = 3600; // 1 heure
    const CACHE_KEY_DOCTORS = 'active_doctors';
    const CACHE_KEY_ASSUREURS = 'assureurs';
    const CACHE_KEY_PAYMENT_TYPES = 'types_paiement';
    const CACHE_KEY_ACTES = 'active_actes';

    // États des modales
    public $showCreateModal = false;
    public $showCreateRdvModal = false;
    public $showPatientListModal = false;
    public $showGestionPatientsModal = false;
    public $showActeModal = false;
    public $showAssureurModal = false;
    public $showCreatePatientModal = false;
    public $showPatientDetails = false;
    public $showPaymentHistory = false;
    public $showEditModal = false;
    public $showDeleteConfirmation = false;
    public $showCreateActeModal = false;
    public $showListeActesModal = false;
    public $showCaisseOperations = false;
    public $showUsersModal = false;
    public $showStatistiques = false;
    public $showMedecinsModal = false;
    public $showTypePaiementModal = false;
    public $showCabinetMenu = false;
    public $showPatientMenu = false;

    // États des actions et données
    public $action = null;
    public $isDocteurProprietaire = false;
    public $isDocteur = false;
    public $isSecretaire = false;
    public $canManageRdv = false;
    public $canViewAllRdv = false;
    public $selectedPatient = null;
    public $editingPatient;
    public $patientToDelete;

    // Données mises en cache
    public $medecins;
    public $assureurs;
    public $typesPaiement;
    public $actes;

    protected $listeners = [
        'patientSelected' => 'setPatient',
        'patientCreated' => 'handlePatientCreated',
        'closeCreateModal' => 'closeCreateModal',
        'assureurCreated' => 'handleAssureurCreated',
        'acteCreated' => 'handleActeCreated',
        'refreshData' => 'refreshCachedData',
        'openModal' => 'handleOpenModal'
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->initializeRoles();
        $this->loadCachedData();
        $this->preloadActes();
        
        // Initialisation explicite des propriétés de menu
        $this->showPatientMenu = false;
        $this->showCabinetMenu = false;

        if (request()->has('patient_id')) {
            $patient = Patient::find(request()->patient_id);
            if ($patient) {
                $this->setPatient($patient);
            }
        }
    }

    private function initializeRoles()
    {
        $user = Auth::user();
        
        $this->isDocteurProprietaire = $user->isDocteurProprietaire();
        $this->isDocteur = $user->isDocteur();
        $this->isSecretaire = $user->isSecretaire();

        $this->canManageRdv = $this->isDocteurProprietaire || $this->isSecretaire || $this->isDocteur;
        $this->canViewAllRdv = $this->isDocteurProprietaire || $this->isSecretaire;
    }

    private function loadCachedData()
    {
        $this->medecins = Cache::remember(self::CACHE_KEY_DOCTORS, self::CACHE_TTL, function () {
            $query = User::whereHas('typeuser', function ($query) {
                $query->whereIn('Libelle', ['Docteur Propriétaire', 'Docteur']);
            })->where('ismasquer', 0);

            if ($this->isDocteur) {
                $query->where('Iduser', Auth::id());
            }

            return $query->get();
        });

        $this->assureurs = Cache::remember(self::CACHE_KEY_ASSUREURS, self::CACHE_TTL, function () {
            return Assureur::all();
        });

        $this->typesPaiement = Cache::remember(self::CACHE_KEY_PAYMENT_TYPES, self::CACHE_TTL, function () {
            return RefTypePaiement::all();
        });
    }

    private function preloadActes()
    {
        return Cache::remember(self::CACHE_KEY_ACTES, self::CACHE_TTL, function () {
            return Acte::where('Masquer', 0)
                      ->orderBy('nordre')
                      ->get();
        });
    }

    public function handleOpenModal($modalName)
    {
        switch ($modalName) {
            case 'liste-actes':
                $this->showListeActesModal = true;
                break;
            // Ajoutez d'autres cas si nécessaire
        }
    }

    public function ouvrirListeActesModal()
    {
        $this->showListeActesModal = true;
        $this->showCaisseOperations = false;
        $this->showStatistiques = false;
        $this->emit('listeActesModalOpened');
    }

    public function fermerListeActesModal()
    {
        $this->showListeActesModal = false;
    }

    public function refreshCachedData()
    {
        Cache::forget(self::CACHE_KEY_DOCTORS);
        Cache::forget(self::CACHE_KEY_ASSUREURS);
        Cache::forget(self::CACHE_KEY_PAYMENT_TYPES);
        Cache::forget(self::CACHE_KEY_ACTES);
        $this->loadCachedData();
        $this->preloadActes();
    }

    public function resetState()
    {
        $this->reset([
            'showCreateModal',
            'showPatientListModal',
            'showGestionPatientsModal',
            'showActeModal',
            'showAssureurModal',
            'showCreatePatientModal',
            'showPatientDetails',
            'showPaymentHistory',
            'showEditModal',
            'showDeleteConfirmation',
            'showCreateActeModal',
            'showListeActesModal',
            'showCaisseOperations',
            'showPatientMenu',
            'showCabinetMenu',
            'action',
            'editingPatient',
            'patientToDelete'
        ]);
        
        // Réinitialisation manuelle des propriétés qui ne sont pas dans reset()
        $this->showStatistiques = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
    }

    public function closeAllSections()
    {
        $this->showCaisseOperations = false;
        $this->showStatistiques = false;
        $this->showPatientMenu = false;
        $this->showCabinetMenu = false;
        $this->action = null;
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
    }

    public function setAction($action)
    {
        if (!$this->selectedPatient && in_array($action, ['consultation', 'reglement', 'rendezvous'])) {
            return;
        }
        $this->action = $action;
        $this->showCaisseOperations = false;
        $this->showStatistiques = false;
        $this->showCabinetMenu = false;
        // Ne pas fermer le menu patient pour les actions du sous-menu
        // $this->showPatientMenu = false;
        $this->emit('actionChanged', $action);
    }

    public function setPatient($patient)
    {
        $this->selectedPatient = $patient;
        $this->action = null;
        $this->showPatientMenu = false;
        $this->showCabinetMenu = false;
        
        // Réinitialiser l'état sans affecter le modal de création de rendez-vous
        $this->resetState();
    }

    // Gestionnaires d'événements
    public function handlePatientCreated($patientId)
    {
        $patient = Patient::find($patientId);
        if ($patient) {
            $this->setPatient($patient);
            $this->showCreatePatientModal = false;
            $this->emit('patientCreated', $patientId);
        }
    }

    public function handleAssureurCreated()
    {
        $this->refreshCachedData();
        $this->showAssureurModal = false;
    }

    public function handleActeCreated()
    {
        $this->refreshCachedData();
        $this->showActeModal = false;
    }

    // Méthodes de gestion des modales
    public function openGestionPatientsModal()
    {
        $this->showGestionPatientsModal = true;
    }

    public function closeGestionPatientsModal()
    {
        $this->showGestionPatientsModal = false;
    }

    public function showCreateRdv()
    {
        $this->showCreateRdvModal = true;
        // Précharger les données essentielles
        $this->dispatchBrowserEvent('modal-loading', ['loading' => true]);
        
        // Émettre un événement pour informer le composant CreateRendezVous du patient sélectionné
        if ($this->selectedPatient) {
            $this->emit('patientSelected', $this->selectedPatient);
        }
    }

    public function closeCreateRdvModal()
    {
        $this->showCreateRdvModal = false;
    }

    public function showCaisseOperations()
    {
        $this->closeAllSections();
        $this->showCaisseOperations = true;
    }

    public function ouvrirAssureurModal()
    {
        $this->showAssureurModal = true;
        $this->showStatistiques = false;
    }

    public function fermerAssureurModal()
    {
        $this->showAssureurModal = false;
    }

    public function showCreatePatient()
    {
        $this->showCreatePatientModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function openPatientListModal()
    {
        $this->showPatientListModal = true;
    }

    public function closePatientListModal()
    {
        $this->showPatientListModal = false;
    }

    public function ouvrirActeModal()
    {
        $this->showActeModal = true;
    }

    public function fermerActeModal()
    {
        $this->showActeModal = false;
    }

    public function openUsersModal()
    {
        $this->showUsersModal = true;
        $this->showStatistiques = false;
    }

    public function closeUsersModal()
    {
        $this->showUsersModal = false;
    }

    public function showStatistiques()
    {
        $this->closeAllSections();
        $this->showStatistiques = true;
    }

    public function ouvrirMedecinsModal()
    {
        $this->showMedecinsModal = true;
    }

    public function fermerMedecinsModal()
    {
        $this->showMedecinsModal = false;
    }

    public function ouvrirTypePaiementModal()
    {
        $this->showTypePaiementModal = true;
    }

    public function fermerTypePaiementModal()
    {
        $this->showTypePaiementModal = false;
    }

    public function toggleCabinetMenu()
    {
        $this->showCabinetMenu = !$this->showCabinetMenu;
        if ($this->showCabinetMenu) {
            $this->closeAllSections();
            $this->showCabinetMenu = true;
        }
    }

    public function togglePatientMenu()
    {
        // Vérifier si un patient est sélectionné
        if (!$this->selectedPatient) {
            return;
        }
        
        $this->showPatientMenu = !$this->showPatientMenu;
        if ($this->showPatientMenu) {
            $this->closeAllSections();
            $this->showPatientMenu = true;
        }
    }

    public function render()
    {
        return view('livewire.accueil-patient');
    }
} 