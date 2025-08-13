<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rendezvou;
use App\Models\Patient;
use App\Models\Medecin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helpers\QrCodeHelper;

class RdvReminders extends Component
{
    use WithPagination;

    // Propriétés pour les filtres
    public $dateFilter = '';
    public $medecinFilter = '';
    public $searchPatient = '';
    
    // Propriétés pour les permissions
    public $isDocteurProprietaire = false;
    public $isSecretaire = false;
    public $isDocteur = false;
    public $canViewAllRdv = false;

    // Propriétés pour les médecins
    public $medecins = [];

    protected $listeners = [
        'refreshReminders' => '$refresh'
    ];

    public function mount()
    {
        $this->initializePermissions();
        $this->loadMedecins();
        $this->dateFilter = now()->addDay()->format('Y-m-d'); // Demain par défaut
    }

    protected function initializePermissions()
    {
        $user = Auth::user();
        
        $this->isDocteurProprietaire = $user->isDocteurProprietaire();
        $this->isSecretaire = $user->isSecretaire();
        $this->isDocteur = $user->isDocteur() && !$user->isDocteurProprietaire();
        $this->canViewAllRdv = $this->isDocteurProprietaire || $this->isSecretaire;
    }

    protected function loadMedecins()
    {
        $this->medecins = Medecin::select('idMedecin', 'Nom')
            ->where('Masquer', 0)
            ->orderBy('Nom')
            ->get();
    }

    public function sendReminder($rdvId)
    {
        try {
            $rdv = Rendezvou::with(['patient', 'medecin'])->find($rdvId);
            
            if (!$rdv) {
                session()->flash('error', 'Rendez-vous non trouvé.');
                return;
            }

            if (!$rdv->patient) {
                session()->flash('error', 'Patient non trouvé pour ce rendez-vous.');
                return;
            }

            // Vérifier que le patient a un numéro de téléphone
            if (empty($rdv->patient->Telephone1)) {
                session()->flash('error', 'Le patient n\'a pas de numéro de téléphone enregistré.');
                return;
            }

            // Générer le message de rappel
            $message = $this->generateReminderMessage($rdv);
            
            // Nettoyer le numéro de téléphone
            $phoneNumber = QrCodeHelper::formatPhoneForWhatsApp($rdv->patient->Telephone1);
            
            // Créer le lien WhatsApp
            $whatsappUrl = "https://wa.me/{$phoneNumber}?text=" . urlencode($message);
            
            // Ouvrir WhatsApp dans un nouvel onglet
            $this->dispatchBrowserEvent('open-whatsapp-reminder', [
                'url' => $whatsappUrl,
                'patientName' => $rdv->patient->Prenom . ' ' . $rdv->patient->Nom,
                'rdvDate' => Carbon::parse($rdv->dtPrevuRDV)->format('d/m/Y'),
                'rdvTime' => Carbon::parse($rdv->HeureRdv)->format('H:i')
            ]);

            // Marquer le rappel comme envoyé (optionnel)
            $rdv->update([
                'rdvConfirmer' => 'Rappel envoyé'
            ]);

            session()->flash('success', 'Rappel WhatsApp envoyé pour ' . $rdv->patient->Prenom . ' ' . $rdv->patient->Nom);
            
            // Rafraîchir le compteur de rappels
            $this->emit('refreshReminders');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'envoi du rappel: ' . $e->getMessage());
        }
    }

    protected function generateReminderMessage($rdv)
    {
        $patientName = $rdv->patient->Prenom . ' ' . $rdv->patient->Nom;
        $rdvDate = Carbon::parse($rdv->dtPrevuRDV)->format('d/m/Y');
        $rdvTime = Carbon::parse($rdv->HeureRdv)->format('H:i');
        $medecinName = $rdv->medecin ? 'Dr. ' . $rdv->medecin->Nom : 'le médecin';
        $acte = $rdv->ActePrevu ?: 'Consultation';

        // Générer le lien de suivi de la file d'attente
        $token = \App\Http\Controllers\PatientInterfaceController::generateToken(
            $rdv->patient->ID, 
            $rdv->dtPrevuRDV, 
            $rdv->fkidMedecin
        );
        $queueLink = url("/patient/rendez-vous/{$token}");

        // Message en arabe d'abord, puis français
        $message = "🔔 *تذكير بالموعد* 🔔\n\n";
        $message .= "مرحباً *{$patientName}*،\n\n";
        $message .= "نذكركم بموعدكم :\n";
        $message .= "📅 *التاريخ :* {$rdvDate}\n";
        $message .= "🕐 *الوقت :* {$rdvTime}\n";
        $message .= "👨‍⚕️ *الطبيب :* {$medecinName}\n";
        $message .= "🦷 *العملية :* {$acte}\n\n";
        $message .= "⚠️ *يرجى تأكيد حضوركم بالرد على هذه الرسالة.*\n\n";
        $message .= "*رابط متابعة طابور الانتظار:*\n";
        $message .= "هذا الرابط يسمح لك بمتابعة موقعك في طابور الانتظار يوم الموعد\n";
        $message .= "{$queueLink}\n\n";
        $message .= "───────────────────\n\n";
        $message .= "🔔 *RAPPEL RENDEZ-VOUS* 🔔\n\n";
        $message .= "Bonjour *{$patientName}*,\n\n";
        $message .= "Nous vous rappelons votre rendez-vous :\n";
        $message .= "📅 *Date :* {$rdvDate}\n";
        $message .= "🕐 *Heure :* {$rdvTime}\n";
        $message .= "👨‍⚕️ *Médecin :* {$medecinName}\n";
        $message .= "🦷 *Acte :* {$acte}\n\n";
        $message .= "⚠️ *Veuillez confirmer votre présence en répondant à ce message.*\n\n";
        $message .= "*Lien de suivi de la file d'attente:*\n";
        $message .= "Ce lien vous permet de suivre votre position dans la file d'attente le jour du rendez-vous\n";
        $message .= "{$queueLink}\n\n";
        $message .= "شكراً / Merci";

        return $message;
    }

    public function updatedDateFilter()
    {
        $this->resetPage();
    }

    public function updatedMedecinFilter()
    {
        $this->resetPage();
    }

    public function updatedSearchPatient()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Rendezvou::with(['patient', 'medecin'])
            ->where('rdvConfirmer', '!=', 'Terminé')
            ->where('rdvConfirmer', '!=', 'Annulé')
            ->where('rdvConfirmer', '!=', 'Rappel envoyé'); // Exclure les RDV qui ont déjà reçu un rappel

        // Filtrer par date
        if ($this->dateFilter) {
            $query->whereDate('dtPrevuRDV', $this->dateFilter);
        }

        // Filtrer par médecin
        if ($this->medecinFilter) {
            $query->where('fkidMedecin', $this->medecinFilter);
        }

        // Si c'est un docteur simple, ne montrer que ses rendez-vous
        if ($this->isDocteur && !$this->canViewAllRdv) {
            $query->where('fkidMedecin', Auth::user()->fkidmedecin);
        }

        // Filtrer par cabinet
        $query->where('fkidcabinet', Auth::user()->fkidcabinet);

        // Recherche par patient
        if ($this->searchPatient) {
            $query->whereHas('patient', function($q) {
                $q->where('Nom', 'like', '%' . $this->searchPatient . '%')
                  ->orWhere('Prenom', 'like', '%' . $this->searchPatient . '%')
                  ->orWhere('Telephone1', 'like', '%' . $this->searchPatient . '%');
            });
        }

        // Trier par date et heure
        $query->orderBy('dtPrevuRDV', 'asc')
              ->orderBy('HeureRdv', 'asc');

        $rendezVous = $query->paginate(10);

        return view('livewire.rdv-reminders', [
            'rendezVous' => $rendezVous
        ]);
    }
}
