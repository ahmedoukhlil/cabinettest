<div class="w-full px-4 md:px-6 lg:px-8 max-w-7xl mx-auto mt-4 md:mt-10">
    {{-- Bannière de bienvenue --}}
    <div class="mb-4 md:mb-8 p-4 md:p-6 rounded-xl bg-primary text-white shadow-lg flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-center md:text-left">
            <h1 class="text-2xl md:text-3xl font-bold mb-1">
                Cabinet Dentaire
            </h1>
            <p class="text-primary-light text-base md:text-lg">
                    {{ is_array(Auth::user()->typeuser) ? (Auth::user()->typeuser['Libelle'] ?? '') : (is_object(Auth::user()->typeuser) ? Auth::user()->typeuser->Libelle : Auth::user()->typeuser) }}
                <span class="font-bold">
                    {{ Auth::user()->NomComplet ?? Auth::user()->name ?? '' }}
                </span>
            </p>
        </div>
        <i class="fas fa-tooth text-4xl md:text-5xl opacity-30"></i>
    </div>

    {{-- Encadré recherche patient + nouveau patient --}}
    <div class="bg-white rounded-xl shadow p-4 md:p-6 flex flex-col md:flex-row items-center gap-4 md:gap-6 mb-4 md:mb-8 border border-primary-light">
        <div class="w-full">
            <livewire:patient-search />
        </div>
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <button wire:click="openGestionPatientsModal" class="w-full sm:w-auto px-4 md:px-6 py-2 md:py-3 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl transition-all duration-300 ease-in-out text-base md:text-lg flex items-center justify-center gap-2">
                <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-300 ease-in-out">
                    <i class="fas fa-users text-primary text-xl md:text-2xl transition-all duration-300 ease-in-out"></i>
                </span>
                <span class="font-semibold transition-all duration-300 ease-in-out">Liste de patients</span>
            </button>
                         <button wire:click="showCreateRdv" class="w-full sm:w-auto px-4 md:px-6 py-2 md:py-3 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl transition-all duration-300 ease-in-out text-base md:text-lg flex items-center justify-center gap-2">
                 <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-300 ease-in-out">
                     <i class="fas fa-calendar-plus text-primary text-xl md:text-2xl transition-all duration-300 ease-in-out"></i>
                 </span>
                 <span class="font-semibold transition-all duration-300 ease-in-out">Gestion RDV</span>
             </button>
        </div>
    </div>

    @if($isDocteurProprietaire || $isDocteur || $isSecretaire)
        <div class="flex flex-wrap gap-2 md:gap-4 mb-4 md:mb-8 bg-gray-50 z-10 py-2 md:py-4 justify-center items-center">
            {{-- Gestion du patient (bouton principal) --}}
            <button
                wire:click="togglePatientMenu"
                @if(!$selectedPatient) disabled title="Veuillez sélectionner un patient d'abord" @endif
                class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 rounded-xl shadow-lg hover:shadow-xl transition-all duration-500 ease-out text-base md:text-lg justify-center transform hover:scale-105 active:scale-95 ripple menu-button
                {{ $showPatientMenu ? 'bg-primary text-white border-primary shadow-xl scale-105' : (!$selectedPatient ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-primary border-primary hover:bg-primary hover:text-white') }}">
                <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 transition-all duration-500 ease-out {{ $showPatientMenu ? 'bg-white text-primary rotate-12' : 'bg-white text-primary' }}">
                    <i class="fas fa-user-friends text-primary text-xl md:text-2xl transition-all duration-500 ease-out {{ $showPatientMenu ? 'text-primary' : 'text-primary' }}"></i>
                </span>
                <span class="font-semibold transition-all duration-500 ease-out">Gestion du patient</span>
            </button>
            {{-- Caisse Paie --}}
            <button wire:click="showCaisseOperations"
                class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95">
                <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                    <i class="fas fa-cash-register text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                </span>
                <span class="font-semibold transition-all duration-500 ease-out">Caisse Paie</span>
            </button>
            {{-- Dépenses --}}
            @if($isDocteurProprietaire)
            <button wire:click="openDepenses" class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95">
                <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                    <i class="fas fa-receipt text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                </span>
                <span class="font-semibold transition-all duration-500 ease-out">Dépenses</span>
            </button>
            @endif
            {{-- Statistiques --}}
            @if($isDocteurProprietaire)
            <button wire:click="showStatistiques" class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95">
                <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                    <i class="fas fa-chart-bar text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                </span>
                <span class="font-semibold transition-all duration-500 ease-out">Statistiques</span>
            </button>
            @endif
            {{-- Gestion du cabinet (bouton principal) --}}
            <button wire:click="toggleCabinetMenu"
                class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 rounded-xl shadow-lg hover:shadow-xl transition-all duration-500 ease-out text-base md:text-lg justify-center transform hover:scale-105 active:scale-95 ripple menu-button
                {{ $showCabinetMenu ? 'bg-primary text-white border-primary shadow-xl scale-105' : 'bg-white text-primary border-primary hover:bg-primary hover:text-white' }}">
                <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 transition-all duration-500 ease-out {{ $showCabinetMenu ? 'bg-white text-primary rotate-12' : 'bg-white text-primary' }}">
                    <i class="fas fa-cogs text-primary text-xl md:text-2xl transition-all duration-500 ease-out {{ $showCabinetMenu ? 'text-primary' : 'text-primary' }}"></i>
                </span>
                <span class="font-semibold transition-all duration-500 ease-out">Gestion du cabinet</span>
            </button>
        </div>

                 {{-- Sous-menu Gestion du patient --}}
         @if($showPatientMenu)
         <div class="w-full flex flex-wrap gap-2 md:gap-4 justify-center items-center mt-2 transition-all duration-700 ease-out transform {{ $showPatientMenu ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 -translate-y-4 scale-95' }}" 
              style="animation: slideInDown 0.7s ease-out;" data-menu="patient">
             {{-- Consultation --}}
             <button wire:click="setAction('consultation')"
                 class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95 {{ !$selectedPatient ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed hover:scale-100' : '' }}">
                 <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                     <i class="fas fa-stethoscope text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                 </span>
                 <span class="font-semibold transition-all duration-500 ease-out">Consultation</span>
             </button>
             {{-- Facture/Devis --}}
             <button wire:click="setAction('reglement')"
                 class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95 {{ !$selectedPatient ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed hover:scale-100' : '' }}">
                 <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                     <i class="fas fa-file-invoice-dollar text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                 </span>
                 <span class="font-semibold transition-all duration-500 ease-out">Facture/Devis</span>
             </button>
             {{-- Rendez-vous --}}
             <button wire:click="setAction('rendezvous')"
                 class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95 {{ !$selectedPatient ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed hover:scale-100' : '' }}">
                 <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                     <i class="fas fa-calendar-check text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                 </span>
                 <span class="font-semibold transition-all duration-500 ease-out">Rendez-vous</span>
             </button>
             {{-- Dossier médical --}}
             <button wire:click="setAction('dossier')"
                 class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95 {{ !$selectedPatient ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed hover:scale-100' : '' }}">
                 <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                     <i class="fas fa-folder-medical text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                 </span>
                 <span class="font-semibold transition-all duration-500 ease-out">Dossier médical</span>
             </button>
         </div>
         @endif

        @if($showCaisseOperations)
            <div class="w-full">
                <livewire:caisse-operations-manager wire:key="caisse-operations" />
            </div>
        @endif

        @if($showDepenses)
            <div class="w-full">
                <livewire:depenses-manager wire:key="depenses-manager" />
            </div>
        @endif
    @endif

    {{-- Modal création patient --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop animate-backdrop-fade-in">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-0 relative modal-container animate-modal-fade-in">
                <!-- Header du modal -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t-2xl modal-header sticky top-0 z-10">
                    <div class="flex items-center">
                        <i class="fas fa-users header-icon"></i>
                        <h2 class="text-xl font-bold text-primary">Gestion des patients</h2>
                    </div>
                    <button wire:click="closeCreateModal" class="text-gray-500 hover:text-primary text-2xl flex items-center gap-2 modal-close-button fixed top-4 right-4 z-20 bg-white rounded-full p-2 shadow-lg animate-close-button-appear">
                        <i class="fas fa-times"></i> <span class="text-base font-medium">Fermer</span>
                    </button>
                </div>
                <!-- Contenu scrollable -->
                <div class="max-h-[70vh] overflow-y-auto p-6 modal-body pt-16">
                    <livewire:patient-manager />
                </div>
            </div>
        </div>
    @endif

    {{-- Modal création RDV pour tous patients --}}
    @if($showCreateRdvModal)
        <div class="fixed inset-0 z-50 overflow-y-auto modal-backdrop animate-backdrop-fade-in">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full modal-content animate-modal-fade-in">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t-2xl modal-header sticky top-0 z-10">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt header-icon"></i>
                            <h2 class="text-xl font-bold text-primary">Gestion des Rendez-vous</h2>
                        </div>
                        <button type="button" wire:click="closeCreateRdvModal" class="text-gray-500 hover:text-primary text-2xl flex items-center gap-2 modal-close-button fixed top-4 right-4 z-20 bg-white rounded-full p-2 shadow-lg animate-close-button-appear">
                            <i class="fas fa-times"></i> <span class="text-base font-medium">Fermer</span>
                        </button>
                    </div>
                    
                                                                                                                                                                       <!-- Onglets -->
                       <div class="bg-white border-b border-gray-200">
                           <nav class="flex space-x-8 px-6" aria-label="Tabs">
                                                            <button wire:click="$set('activeRdvTab', 'create')" 
                                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm {{ $activeRdvTab === 'create' ? 'border-white text-white active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                        style="{{ $activeRdvTab === 'create' ? 'background-color: #1e3a8a !important;' : '' }}">
                                    <i class="fas fa-plus mr-2"></i>
                                    Gestion RDV
                                </button>
                                                            <button wire:click="$set('activeRdvTab', 'reminders')" 
                                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm {{ $activeRdvTab === 'reminders' ? 'border-white text-white active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} relative"
                                        style="{{ $activeRdvTab === 'reminders' ? 'background-color: #1e3a8a !important;' : '' }}">
                                    <i class="fas fa-bell mr-2"></i>
                                    Rappels RDV
                                    @if($rdvRemindersCount > 0)
                                        <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                            {{ $rdvRemindersCount }}
                                        </span>
                                    @endif
                                </button>
                           </nav>
                       </div>
                    
                                         <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 modal-body pt-16">
                         <div id="modal-loading" class="hidden flex items-center justify-center py-8">
                             <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                             <span class="ml-3 text-gray-600">Chargement...</span>
                         </div>
                         
                         <div class="tab-content">
                             @if($activeRdvTab === 'create')
                                 <div class="tab-panel showing" wire:key="create-rdv-panel">
                                     <livewire:create-rendez-vous wire:key="create-rdv-modal" :patient="$selectedPatient" />
                                 </div>
                             @elseif($activeRdvTab === 'reminders')
                                 <div class="tab-panel showing" wire:key="reminders-panel">
                                     <livewire:rdv-reminders wire:key="rdv-reminders-modal" />
                                 </div>
                             @endif
                         </div>
                     </div>
                </div>
            </div>
        </div>
    @endif

    @if($showGestionPatientsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop animate-backdrop-fade-in">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-7xl p-0 relative modal-container animate-modal-fade-in">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t-2xl modal-header sticky top-0 z-10">
                    <div class="flex items-center">
                        <i class="fas fa-users header-icon"></i>
                        <h2 class="text-xl font-bold text-primary">Gestion des patients</h2>
                    </div>
                    <button wire:click="closeGestionPatientsModal" class="text-gray-500 hover:text-primary text-2xl flex items-center gap-2 modal-close-button fixed top-4 right-4 z-20 bg-white rounded-full p-2 shadow-lg animate-close-button-appear">
                        <i class="fas fa-times"></i> <span class="text-base font-medium">Fermer</span>
                    </button>
                </div>
                <div class="max-h-[70vh] overflow-y-auto p-6 modal-body pt-16">
                    <livewire:patient-manager />
                </div>
            </div>
        </div>
    @endif



        <!-- Notifications pour les consultations -->
        @if (session()->has('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 class="fixed bottom-4 right-4 bg-primary-light border border-primary text-primary px-4 py-3 rounded shadow-lg z-50" 
                 role="alert">
                <div class="flex items-center">
                    <div class="py-1">
                        <svg class="h-5 w-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="ml-4 text-primary hover:text-primary-dark">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 class="fixed bottom-4 right-4 bg-primary-light border border-primary text-primary px-4 py-3 rounded shadow-lg z-50" 
                 role="alert">
                <div class="flex items-center">
                    <div class="py-1">
                        <svg class="h-5 w-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="ml-4 text-primary hover:text-primary-dark">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

                 <div class="mt-8">
             @if($action === 'consultation')
                 <div wire:loading.remove wire:target="setAction">
                     <livewire:consultation-form :patient="$selectedPatient" wire:key="consultation-{{ $selectedPatient['ID'] ?? 'new' }}" lazy />
                 </div>
                 <div wire:loading wire:target="setAction" class="flex justify-center items-center py-8">
                     <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                 </div>
             @elseif($action === 'reglement')
                 <div wire:loading.remove wire:target="setAction">
                     <livewire:reglement-facture :selectedPatient="$selectedPatient" wire:key="reglement-{{ $selectedPatient['ID'] ?? 'new' }}" lazy />
                 </div>
                 <div wire:loading wire:target="setAction" class="flex justify-center items-center py-8">
                     <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                 </div>
             @elseif($action === 'rendezvous')
                 <div wire:loading.remove wire:target="setAction">
                     <livewire:create-rendez-vous :patient="$selectedPatient" wire:key="rdv-{{ $selectedPatient['ID'] ?? 'new' }}" lazy />
                 </div>
                 <div wire:loading wire:target="setAction" class="flex justify-center items-center py-8">
                     <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                 </div>
             @elseif($action === 'dossier')
                 <div wire:loading.remove wire:target="setAction">
                     <livewire:dossier-medical :patient="$selectedPatient" wire:key="dossier-{{ $selectedPatient['ID'] ?? 'new' }}" lazy />
                 </div>
                 <div wire:loading wire:target="setAction" class="flex justify-center items-center py-8">
                     <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                 </div>
             @endif
         </div>

        <!-- Composant HistoriquePaiement toujours présent -->
        <livewire:historique-paiement wire:key="historique-paiement" lazy />

    @if($showAssureurModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop animate-backdrop-fade-in">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl p-0 relative modal-container animate-modal-fade-in">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-primary-light rounded-t-2xl modal-header sticky top-0 z-10">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt header-icon"></i>
                        <h2 class="text-xl font-bold text-primary">Gestion des assurances</h2>
                    </div>
                    <button wire:click="fermerAssureurModal" class="text-gray-500 hover:text-primary text-2xl flex items-center gap-2 modal-close-button fixed top-4 right-4 z-20 bg-white rounded-full p-2 shadow-lg animate-close-button-appear">
                        <i class="fas fa-times"></i> <span class="text-base font-medium">Fermer</span>
                    </button>
                </div>
                <div class="max-h-[70vh] overflow-y-auto p-6 modal-body pt-16">
                    <livewire:assureur-manager wire:key="assureur-manager-modal" />
                </div>
            </div>
        </div>
    @endif

    @if($showListeActesModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop animate-backdrop-fade-in">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-7xl p-0 relative modal-container animate-modal-fade-in">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t-2xl modal-header sticky top-0 z-10">
                    <div class="flex items-center">
                        <i class="fas fa-stethoscope header-icon"></i>
                        <h2 class="text-xl font-bold text-primary">Liste de soins</h2>
                    </div>
                    <button wire:click="fermerListeActesModal" class="text-gray-500 hover:text-primary text-2xl flex items-center gap-2 modal-close-button fixed top-4 right-4 z-20 bg-white rounded-full p-2 shadow-lg animate-close-button-appear">
                        <i class="fas fa-times"></i> <span class="text-base font-medium">Fermer</span>
                    </button>
                </div>
                <div class="max-h-[70vh] overflow-y-auto p-6 modal-body pt-16">
                    <livewire:acte-manager />
                </div>
            </div>
        </div>
    @endif

    {{-- Modal utilisateurs --}}
    @if($showUsersModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop animate-backdrop-fade-in">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-0 relative modal-container animate-modal-fade-in">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t-2xl modal-header sticky top-0 z-10">
                    <div class="flex items-center">
                        <i class="fas fa-user-cog header-icon"></i>
                        <h2 class="text-xl font-bold text-primary">Gestion des utilisateurs</h2>
                    </div>
                    <button wire:click="closeUsersModal" class="text-gray-500 hover:text-primary text-2xl flex items-center gap-2 modal-close-button fixed top-4 right-4 z-20 bg-white rounded-full p-2 shadow-lg animate-close-button-appear">
                        <i class="fas fa-times"></i> <span class="text-base font-medium">Fermer</span>
                    </button>
                </div>
                <div class="max-h-[70vh] overflow-y-auto p-6 modal-body pt-16">
                    <livewire:user-manager />
                </div>
            </div>
        </div>
    @endif

    @if($isDocteurProprietaire && $showStatistiques)
        <div class="w-full mt-8">
            <livewire:statistiques-manager wire:key="statistiques-manager" />
        </div>
    @endif

    {{-- Modal gestion des médecins --}}
    @if($showMedecinsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop animate-backdrop-fade-in">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-0 relative modal-container animate-modal-fade-in">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t-2xl modal-header sticky top-0 z-10">
                    <div class="flex items-center">
                        <i class="fas fa-user-md header-icon"></i>
                        <h2 class="text-xl font-bold text-primary">Gestion des médecins</h2>
                    </div>
                    <button wire:click="fermerMedecinsModal" class="text-gray-500 hover:text-primary text-2xl flex items-center gap-2 modal-close-button fixed top-4 right-4 z-20 bg-white rounded-full p-2 shadow-lg animate-close-button-appear">
                        <i class="fas fa-times"></i> <span class="text-base font-medium">Fermer</span>
                    </button>
                </div>
                <div class="max-h-[70vh] overflow-y-auto p-6 modal-body pt-16">
                    <livewire:medecin-manager />
                </div>
            </div>
        </div>
    @endif

    {{-- Modal gestion des types de paiement --}}
    @if($showTypePaiementModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop animate-backdrop-fade-in">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl p-0 relative modal-container animate-modal-fade-in">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t-2xl modal-header sticky top-0 z-10">
                    <div class="flex items-center">
                        <i class="fas fa-credit-card header-icon"></i>
                        <h2 class="text-xl font-bold text-primary">Gestion des types de paiement</h2>
                    </div>
                    <button wire:click="fermerTypePaiementModal" class="text-gray-500 hover:text-primary text-2xl flex items-center gap-2 modal-close-button fixed top-4 right-4 z-20 bg-white rounded-full p-2 shadow-lg animate-close-button-appear">
                        <i class="fas fa-times"></i> <span class="text-base font-medium">Fermer</span>
                    </button>
                </div>
                <div class="max-h-[70vh] overflow-y-auto p-6 modal-body pt-16">
                    <livewire:type-paiement-manager />
                </div>
            </div>
        </div>
    @endif

    {{-- Sous-menu Gestion du cabinet --}}
    @if($showCabinetMenu)
    <div class="w-full flex flex-wrap gap-2 md:gap-4 justify-center items-center mt-2 transition-all duration-700 ease-out transform {{ $showCabinetMenu ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 -translate-y-4 scale-95' }}" 
         style="animation: slideInDown 0.7s ease-out;" data-menu="cabinet">
        {{-- Assurances --}}
        <button wire:click="ouvrirAssureurModal"
            class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95">
            <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                <i class="fas fa-house-user text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
            </span>
            <span class="font-semibold transition-all duration-500 ease-out">Assurances</span>
        </button>
        {{-- Liste de soins --}}
        <button wire:click="ouvrirListeActesModal"
            class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95">
            <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                <i class="fas fa-hospital-user text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
            </span>
            <span class="font-semibold transition-all duration-500 ease-out">Soins</span>
        </button>
        {{-- Utilisateurs --}}
        @if($isDocteurProprietaire)
        <button wire:click="openUsersModal"
            class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95">
            <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                <i class="fas fa-users-cog text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
            </span>
            <span class="font-semibold transition-all duration-500 ease-out">Utilisateurs</span>
        </button>
        @endif
        {{-- Gestion des médecins --}}
        <button wire:click="ouvrirMedecinsModal"
            class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95">
            <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                <i class="fas fa-user-md text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
            </span>
            <span class="font-semibold transition-all duration-500 ease-out">Médecins</span>
        </button>
        {{-- Gestion des types de paiement --}}
        <button wire:click="ouvrirTypePaiementModal"
            class="flex items-center gap-2 md:gap-3 px-3 md:px-6 py-2 md:py-3 w-full sm:w-48 md:w-56 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-base md:text-lg justify-center transform active:scale-95">
            <span class="inline-flex items-center justify-center rounded-full p-1 md:p-2 bg-white text-primary transition-all duration-500 ease-out">
                <i class="fas fa-money-check-alt text-primary text-xl md:text-2xl transition-all duration-500 ease-out"></i>
            </span>
            <span class="font-semibold transition-all duration-500 ease-out">Modes Paiements</span>
        </button>
    </div>
    @endif


</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('openModal', (modalName) => {
            console.log('Opening modal:', modalName);
        });

        // Gestion des animations de menu
        window.addEventListener('menu-opened', function(event) {
            const menu = event.detail.menu;
            console.log('Menu opened:', menu);
            
            // Ajouter une classe pour l'animation d'entrée
            const menuElement = document.querySelector(`[data-menu="${menu}"]`);
            if (menuElement) {
                menuElement.classList.add('animate-slide-in-down');
                menuElement.classList.add('gpu-accelerated');
            }
        });

        window.addEventListener('menu-closed', function(event) {
            const menu = event.detail.menu;
            console.log('Menu closed:', menu);
            
            // Ajouter une classe pour l'animation de sortie
            const menuElement = document.querySelector(`[data-menu="${menu}"]`);
            if (menuElement) {
                menuElement.classList.add('animate-slide-out-up');
                setTimeout(() => {
                    menuElement.classList.remove('animate-slide-out-up');
                }, 500);
            }
        });

        // Optimisation des transitions Livewire
        Livewire.hook('message.processed', (message, component) => {
            // Ajouter des classes d'animation aux nouveaux éléments
            const newElements = component.el.querySelectorAll('[data-animate]');
            newElements.forEach(element => {
                const animation = element.getAttribute('data-animate');
                element.classList.add(animation);
            });
        });

        // Effet de ripple pour les boutons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.ripple')) {
                const button = e.target.closest('.ripple');
                const ripple = document.createElement('span');
                const rect = button.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple-effect');
                
                button.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            }
        });

        // Gestion des animations de fermeture des modaux - Version fluide
        document.addEventListener('click', function(e) {
            if (e.target.closest('.modal-close-button')) {
                e.preventDefault();
                e.stopPropagation();
                
                const closeButton = e.target.closest('.modal-close-button');
                const modal = closeButton.closest('.modal-container');
                const backdrop = modal.parentElement;
                
                // Empêcher les clics multiples
                if (modal.classList.contains('closing')) return;
                modal.classList.add('closing');
                
                // Animation de fermeture fluide
                modal.classList.remove('animate-modal-fade-in');
                modal.classList.add('animate-modal-fade-out');
                
                backdrop.classList.remove('animate-backdrop-fade-in');
                backdrop.classList.add('animate-backdrop-fade-out');
                
                // Animation du bouton de fermeture (sans rotation)
                // closeButton.classList.add('animate-close-button-spin');
                
                // Attendre la fin de l'animation avant de fermer
                setTimeout(() => {
                    // Le modal sera fermé par Livewire
                    modal.classList.remove('closing');
                }, 600);
            }
        });

        // Animation des boutons de fermeture au hover (sans rotation)
        document.addEventListener('mouseenter', function(e) {
            if (e.target.closest('.modal-close-button')) {
                const button = e.target.closest('.modal-close-button');
                button.style.transform = 'scale(1.1)';
                button.style.color = '#ef4444';
            }
        });

        document.addEventListener('mouseleave', function(e) {
            if (e.target.closest('.modal-close-button')) {
                const button = e.target.closest('.modal-close-button');
                button.style.transform = 'scale(1)';
                button.style.color = '';
            }
        });
    });

    document.addEventListener('alpine:init', () => {
        Alpine.data('modal', () => ({
            show: false,
            open() {
                this.show = true;
            },
            close() {
                this.show = false;
            }
        }));

        Alpine.data('menuAnimation', () => ({
            isOpen: false,
            toggle() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.$nextTick(() => {
                        this.$el.classList.add('animate-slide-in-down');
                    });
                } else {
                    this.$el.classList.add('animate-slide-out-up');
                    setTimeout(() => {
                        this.$el.classList.remove('animate-slide-out-up');
                    }, 500);
                }
            }
        }));
    });
    
    // Gestion du chargement du modal
    window.addEventListener('modal-loading', function(e) {
        const loadingDiv = document.getElementById('modal-loading');
        const contentDiv = document.querySelector('livewire\\:create-rendez-vous');
        
        if (e.detail.loading) {
            if (loadingDiv) loadingDiv.classList.remove('hidden');
            if (contentDiv) contentDiv.style.display = 'none';
        } else {
            if (loadingDiv) loadingDiv.classList.add('hidden');
            if (contentDiv) contentDiv.style.display = 'block';
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
            const statutSelect = document.getElementById('rdv_confirm');
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

         // Optimisation des performances
     document.addEventListener('DOMContentLoaded', function() {
         // Utiliser requestAnimationFrame pour les animations fluides
         const observerOptions = {
             threshold: 0.1,
             rootMargin: '0px 0px -50px 0px'
         };

         const observer = new IntersectionObserver((entries) => {
             entries.forEach(entry => {
                 if (entry.isIntersecting) {
                     entry.target.classList.add('animate-fade-in-scale');
                 }
             });
         }, observerOptions);

         // Observer les éléments avec animation
         document.querySelectorAll('[data-animate-on-scroll]').forEach(el => {
             observer.observe(el);
         });
     });

     // Gestion des transitions fluides entre les onglets
     document.addEventListener('livewire:load', function() {
         // Écouter les changements d'onglets
         Livewire.hook('message.processed', (message, component) => {
             // Vérifier si c'est un changement d'onglet
             if (message.updateQueue && message.updateQueue.some(update => 
                 update.type === 'callMethod' && 
                 (update.payload.method === '$set' && update.payload.params && update.payload.params[0] === 'activeRdvTab')
             )) {
                 
                 // Ajouter une animation de transition
                 const tabContent = document.querySelector('.tab-content');
                 const tabPanels = document.querySelectorAll('.tab-panel');
                 
                 if (tabContent && tabPanels.length > 0) {
                     // Animation de sortie pour l'ancien contenu
                     tabPanels.forEach(panel => {
                         if (panel.classList.contains('showing')) {
                             panel.classList.remove('showing');
                             panel.classList.add('hiding');
                             
                             setTimeout(() => {
                                 panel.classList.remove('hiding');
                             }, 300);
                         }
                     });
                     
                     // Animation d'entrée pour le nouveau contenu
                     setTimeout(() => {
                         const activePanel = document.querySelector('.tab-panel');
                         if (activePanel) {
                             activePanel.classList.add('showing');
                         }
                     }, 150);
                 }
             }
         });

         // Animation des boutons d'onglets au clic
         document.addEventListener('click', function(e) {
             if (e.target.closest('.tab-button')) {
                 const button = e.target.closest('.tab-button');
                 
                 // Ajouter un effet de ripple
                 const ripple = document.createElement('span');
                 const rect = button.getBoundingClientRect();
                 const size = Math.max(rect.width, rect.height);
                 const x = e.clientX - rect.left - size / 2;
                 const y = e.clientY - rect.top - size / 2;
                 
                 ripple.style.width = ripple.style.height = size + 'px';
                 ripple.style.left = x + 'px';
                 ripple.style.top = y + 'px';
                 ripple.classList.add('ripple-effect');
                 
                 button.appendChild(ripple);
                 
                 setTimeout(() => {
                     ripple.remove();
                 }, 600);
             }
         });
     });
    

</script>
@endpush
