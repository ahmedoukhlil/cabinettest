<div class="space-y-6">
    <div class="p-6 rounded-xl bg-primary text-white shadow-lg z-30 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
        <h2 class="text-2xl font-bold">Nouveau Rendez-vous</h2>
        <p class="text-primary-light mt-1">Planifiez un nouveau rendez-vous pour le patient sélectionné</p>
        </div>
        <div class="flex items-center gap-2 mt-4 md:mt-0">
            <span class="text-lg font-semibold">Rdv aujourd'hui :</span>
            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-white text-primary font-bold text-lg shadow">{{ $totalRdvJour }}</span>
        </div>
    </div>

    <!-- Debug: selectedPatient = {{ var_dump($selectedPatient) }} -->
    <!-- Patient sélectionné -->
    @if($selectedPatient)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-user text-blue-600 mr-3"></i>
                    <div>
                        <p class="text-sm text-blue-600 font-medium">Patient sélectionné</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $selectedPatient['Nom'] ?? '' }} {{ $selectedPatient['Prenom'] ?? '' }}</p>
                    </div>
                </div>
                <button type="button" wire:click="handlePatientCleared" class="text-red-600 hover:text-red-800 text-sm font-medium">
                    <i class="fas fa-times mr-1"></i>Changer
                </button>
            </div>
        </div>
    @endif

    <!-- Formulaire de création -->
    <form wire:submit.prevent="createRendezVous" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Recherche de patient -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Rechercher un patient
                    <span class="text-xs text-gray-500 ml-1">(Alt+P)</span>
                </label>
                <div class="relative">
                    <livewire:patient-search wire:key="patient-search-modal" />
                </div>
            </div>

            <!-- Sélection du médecin -->
            <div>
                <label for="medecin_id" class="block text-sm font-medium text-gray-700">Médecin</label>
                <select wire:model="medecin_id" id="medecin_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" {{ $isDocteur ? 'disabled' : '' }}>
                    <option value="">Sélectionner un médecin</option>
                    @foreach($medecins as $medecin)
                        <option value="{{ $medecin->idMedecin }}">Dr. {{ $medecin->Nom }}</option>
                    @endforeach
                </select>
                @error('medecin_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                @if($isDocteur)
                    <p class="mt-1 text-sm text-gray-500">Vous ne pouvez créer des rendez-vous que pour vous-même</p>
                @endif
            </div>

            <!-- Date du rendez-vous -->
            <div>
                <label for="date_rdv" class="block text-sm font-medium text-gray-700">Date</label>
                <input type="date" wire:model="date_rdv" id="date_rdv" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('date_rdv') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Heure du rendez-vous -->
            <div>
                <label for="heure_rdv" class="block text-sm font-medium text-gray-700">Heure</label>
                <input type="time" wire:model="heure_rdv" id="heure_rdv" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('heure_rdv') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Acte prévu -->
            <div>
                <label for="acte_prevu" class="block text-sm font-medium text-gray-700">Acte prévu</label>
                <input type="text" wire:model="acte_prevu" id="acte_prevu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ex: Consultation, Détartrage...">
                @error('acte_prevu') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Statut -->
            <div>
                <label for="rdv_confirmer" class="block text-sm font-medium text-gray-700">Statut</label>
                <select wire:model="rdv_confirmer" id="rdv_confirmer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="En Attente">En Attente</option>
                    <option value="Confirmé">Présent au cabinet</option>
                    <option value="En cours">Avec le médecin</option>
                    <option value="Terminé">Terminé</option>
                    <option value="Annulé">Annulé</option>
                </select>
                @error('rdv_confirmer') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" 
                    wire:loading.attr="disabled" 
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>
                <span wire:loading.remove wire:target="createRendezVous">Créer le rendez-vous</span>
                <span wire:loading wire:target="createRendezVous" class="flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    Création...
                </span>
            </button>
        </div>
    </form>

    <!-- Liste des rendez-vous -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-primary">
            <h3 class="text-lg font-medium text-white">Liste des rendez-vous</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">N°</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médecin</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acte prévu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rendezVous as $rdv)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($rdv->dtPrevuRDV)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($rdv->OrdreRDV)
                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-white bg-blue-600 rounded-full min-w-[2rem]">
                                        {{ str_pad($rdv->OrdreRDV, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-white bg-gray-500 rounded-full min-w-[2rem]">
                                        {{ str_pad($rdv->IDRdv, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($rdv->HeureRdv)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $rdv->patient->Prenom ?? 'Patient inconnu' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Dr. {{ $rdv->medecin->Nom ?? 'Non assigné' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $rdv->ActePrevu }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                    $statusIcon = 'fas fa-clock';
                                    
                                    switch($rdv->rdvConfirmer) {
                                        case 'En Attente':
                                        case 'En attente':
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                            $statusIcon = 'fas fa-clock';
                                            break;
                                        case 'confirmé':
                                        case 'Confirmé':
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                            $statusIcon = 'fas fa-user-check';
                                            break;
                                        case 'En cours':
                                            $statusClass = 'bg-green-100 text-green-800';
                                            $statusIcon = 'fas fa-user-md';
                                            break;
                                        case 'terminé':
                                        case 'Terminé':
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                            $statusIcon = 'fas fa-check-double';
                                            break;
                                        case 'annulé':
                                        case 'Annulé':
                                            $statusClass = 'bg-red-100 text-red-800';
                                            $statusIcon = 'fas fa-times';
                                            break;
                                        case 'Consultation':
                                            $statusClass = 'bg-purple-100 text-purple-800';
                                            $statusIcon = 'fas fa-stethoscope';
                                            break;
                                        default:
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                            $statusIcon = 'fas fa-clock';
                                            break;
                                    }
                                @endphp
                                
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    <i class="{{ $statusIcon }} mr-1"></i>
                                    @switch($rdv->rdvConfirmer)
                                        @case('En Attente')
                                        @case('En attente')
                                            En Attente
                                            @break
                                        @case('confirmé')
                                        @case('Confirmé')
                                            Présent au cabinet
                                            @break
                                        @case('En cours')
                                            Avec le médecin
                                            @break
                                        @case('terminé')
                                        @case('Terminé')
                                            Terminé
                                            @break
                                        @case('annulé')
                                        @case('Annulé')
                                            Annulé
                                            @break
                                        @case('Consultation')
                                            Consultation
                                            @break
                                        @default
                                            En Attente
                                    @endswitch
                                </span>
                            </td>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($canManageRdv)
                                    <div class="flex space-x-1">
                                        <button type="button" wire:click="changerStatutRendezVous({{ $rdv->IDRdv }}, 'Confirmé')" class="inline-flex items-center px-2 py-1 rounded bg-blue-500 text-white text-xs font-semibold hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400" title="Patient présent au cabinet">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                        <button type="button" wire:click="changerStatutRendezVous({{ $rdv->IDRdv }}, 'En cours')" class="inline-flex items-center px-2 py-1 rounded bg-green-500 text-white text-xs font-semibold hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400" title="Avec le médecin">
                                            <i class="fas fa-user-md"></i>
                                        </button>
                                        <button type="button" wire:click="changerStatutRendezVous({{ $rdv->IDRdv }}, 'Terminé')" class="inline-flex items-center px-2 py-1 rounded bg-gray-500 text-white text-xs font-semibold hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400" title="Terminé">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                        <button type="button" wire:click="changerStatutRendezVous({{ $rdv->IDRdv }}, 'Annulé')" class="inline-flex items-center px-2 py-1 rounded bg-red-500 text-white text-xs font-semibold hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400" title="Annulé">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Lecture seule</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Aucun rendez-vous à venir
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $rendezVous->links() }}
        </div>
    </div>

         <!-- Messages de notification -->
     @if (session()->has('message'))
         <div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg">
             {{ session('message') }}
             @if($showPrintButton)
                 <div class="mt-2">
                     <button wire:click="printRendezVous" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                         <i class="fas fa-print mr-1"></i>
                         Imprimer le reçu
                     </button>
                 </div>
             @endif
         </div>
     @endif

     @if (session()->has('error'))
         <div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg">
             {{ session('error') }}
         </div>
     @endif
</div>

<script>
    window.addEventListener('open-receipt', function(e) {
        if (e.detail && e.detail.url) {
            window.open(e.detail.url, '_blank');
        }
    });
    
    // Empêcher la fermeture du modal quand un patient est sélectionné
    window.addEventListener('patient-selected', function(e) {
        console.log('Patient sélectionné dans le modal:', e.detail);
        // Empêcher la propagation d'événements qui pourraient fermer le modal
        e.stopPropagation();
    });
    
    // Empêcher la fermeture du modal sur les clics à l'intérieur
    document.addEventListener('click', function(e) {
        if (e.target.closest('.modal-content')) {
            e.stopPropagation();
        }
    });
    
    // Raccourcis clavier pour accélérer la navigation
    document.addEventListener('keydown', function(e) {
        // Seulement si le modal est ouvert
        if (!document.querySelector('.modal-content')) return;
        
        // Alt + P : Focus sur la recherche de patient
        if (e.altKey && e.key === 'p') {
            e.preventDefault();
            const patientSearch = document.querySelector('input[placeholder*="patient"]');
            if (patientSearch) patientSearch.focus();
        }
        
        // Alt + M : Focus sur la sélection du médecin
        if (e.altKey && e.key === 'm') {
            e.preventDefault();
            const medecinSelect = document.getElementById('medecin_id');
            if (medecinSelect) medecinSelect.focus();
        }
        
        // Alt + D : Focus sur la date
        if (e.altKey && e.key === 'd') {
            e.preventDefault();
            const dateInput = document.getElementById('date_rdv');
            if (dateInput) dateInput.focus();
        }
        
        // Alt + H : Focus sur l'heure
        if (e.altKey && e.key === 'h') {
            e.preventDefault();
            const heureInput = document.getElementById('heure_rdv');
            if (heureInput) heureInput.focus();
        }
        
        // Alt + A : Focus sur l'acte prévu
        if (e.altKey && e.key === 'a') {
            e.preventDefault();
            const acteInput = document.getElementById('acte_prevu');
            if (acteInput) acteInput.focus();
        }
        
        // Alt + S : Focus sur le statut
        if (e.altKey && e.key === 's') {
            e.preventDefault();
            const statutSelect = document.getElementById('rdv_confirmer');
            if (statutSelect) statutSelect.focus();
        }
        
        // Ctrl + Enter : Soumettre le formulaire
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.click();
        }
        
        // Échap : Fermer le modal
        if (e.key === 'Escape') {
            e.preventDefault();
            const closeBtn = document.querySelector('button[wire\\:click*="closeCreateRdvModal"]');
            if (closeBtn) closeBtn.click();
        }
    });
</script>
