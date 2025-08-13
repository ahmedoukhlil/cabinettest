<div class="space-y-4">
    <!-- En-tête -->
    <div class="p-4 rounded-lg bg-primary text-white shadow-md">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-bell mr-2"></i>
                    Rappels de Rendez-vous
                </h2>
                <p class="text-primary-light text-sm mt-1">Envoyez des rappels WhatsApp aux patients pour confirmer leur présence</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold">RDV à rappeler :</span>
                <span class="inline-flex items-center justify-center px-2 py-1 rounded-full bg-red-500 text-white font-bold text-sm shadow">
                    {{ $rendezVous->total() }}
                </span>
            </div>
        </div>
    </div>

    <!-- Messages de succès/erreur -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded text-sm" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded text-sm" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filtres -->
    <div class="bg-primary rounded-lg shadow-sm border border-primary p-4">
        <h3 class="text-base font-semibold text-white mb-3 flex items-center">
            <i class="fas fa-filter mr-2 text-white"></i>
            Filtres
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- Date -->
            <div>
                <label class="block text-xs font-medium text-white mb-1">Date du rendez-vous</label>
                <input type="date" wire:model="dateFilter" 
                       class="w-full px-2 py-1 text-sm border border-white rounded focus:ring-white focus:border-white bg-white">
            </div>

            <!-- Médecin -->
            <div>
                <label class="block text-xs font-medium text-white mb-1">Médecin</label>
                <select wire:model="medecinFilter" 
                        class="w-full px-2 py-1 text-sm border border-white rounded focus:ring-white focus:border-white bg-white">
                    <option value="">Tous les médecins</option>
                    @foreach($medecins as $medecin)
                        <option value="{{ $medecin->idMedecin }}">Dr. {{ $medecin->Nom }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Recherche patient -->
            <div>
                <label class="block text-xs font-medium text-white mb-1">Rechercher un patient</label>
                <input type="text" wire:model.debounce.300ms="searchPatient" 
                       placeholder="Nom, prénom ou téléphone..."
                       class="w-full px-2 py-1 text-sm border border-white rounded focus:ring-white focus:border-white bg-white">
            </div>
        </div>
    </div>

        <!-- Liste des rendez-vous -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-primary">
            <h3 class="text-lg font-medium text-white">Rendez-vous à rappeler</h3>
        </div>
        
        @if($rendezVous->count() > 0)
            <!-- Version mobile - Cartes -->
            <div class="block lg:hidden space-y-3 p-4">
                @foreach($rendezVous as $rdv)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                        <!-- En-tête de la carte -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="text-center">
                                    @if($rdv->OrdreRDV)
                                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-white bg-blue-600 rounded-full min-w-[2rem]">
                                            {{ str_pad($rdv->OrdreRDV, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-white bg-gray-500 rounded-full min-w-[2rem]">
                                            {{ str_pad($rdv->IDRdv, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 text-base">{{ $rdv->patient->Nom ?? 'Patient inconnu' }}</h4>
                                    <p class="text-sm text-gray-500">Dr. {{ $rdv->medecin->Nom ?? 'Non assigné' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($rdv->dtPrevuRDV)->format('d/m/Y') }}
                                </div>
                                <div class="text-lg font-bold text-blue-600">
                                    {{ \Carbon\Carbon::parse($rdv->HeureRdv)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations du RDV -->
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <span class="text-xs text-gray-500 uppercase tracking-wide">Acte prévu</span>
                                <p class="text-sm font-medium text-gray-900 truncate" title="{{ $rdv->ActePrevu ?: 'Consultation' }}">{{ $rdv->ActePrevu ?: 'Consultation' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase tracking-wide">Téléphone</span>
                                <p class="text-sm font-medium text-gray-900">{{ $rdv->patient->Telephone1 ?? 'N/A' }}</p>
                                @if($rdv->patient->Telephone2)
                                    <p class="text-xs text-gray-500">{{ $rdv->patient->Telephone2 }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Statut -->
                        <div class="flex items-center justify-between mb-3">
                            @if($rdv->rdvConfirmer === 'Rappel envoyé')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800" title="Rappel déjà envoyé">
                                    <i class="fas fa-bell mr-1"></i>
                                    Rappelé
                                </span>
                            @else
                                @php
                                    $statusColors = [
                                        'En Attente' => 'bg-yellow-100 text-yellow-800',
                                        'Confirmé' => 'bg-green-100 text-green-800',
                                        'En cours' => 'bg-blue-100 text-blue-800',
                                        'Terminé' => 'bg-gray-100 text-gray-800',
                                        'Annulé' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusColor = $statusColors[$rdv->rdvConfirmer] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ $rdv->rdvConfirmer }}
                                </span>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex justify-center pt-2 border-t border-gray-100">
                            @if($rdv->patient && $rdv->patient->Telephone1)
                                @php
                                    $isRelance = $rdv->rdvConfirmer === 'Rappel envoyé';
                                    $buttonText = $isRelance ? 'Relancer' : 'Rappeler';
                                    $buttonColor = $isRelance ? 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500';
                                @endphp
                                <button wire:click="sendReminder({{ $rdv->IDRdv }})"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-not-allowed"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded text-white {{ $buttonColor }} focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors touch-friendly">
                                    <i class="fab fa-whatsapp mr-2"></i>
                                    <span wire:loading.remove wire:target="sendReminder({{ $rdv->IDRdv }})">
                                        {{ $buttonText }}
                                    </span>
                                    <span wire:loading wire:target="sendReminder({{ $rdv->IDRdv }})" class="flex items-center">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                        Envoi...
                                    </span>
                                </button>
                            @else
                                <span class="text-red-600 text-sm flex items-center justify-center w-full">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Pas de téléphone
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Version desktop - Table -->
            <div class="hidden lg:block overflow-x-auto">
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($rendezVous as $rdv)
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
                                    {{ $rdv->patient->Nom ?? 'Patient inconnu' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Dr. {{ $rdv->medecin->Nom ?? 'Non assigné' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rdv->ActePrevu ?: 'Consultation' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($rdv->rdvConfirmer === 'Rappel envoyé')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800" title="Rappel déjà envoyé">
                                            <i class="fas fa-bell mr-1"></i>
                                            Rappelé
                                        </span>
                                    @else
                                        @php
                                            $statusColors = [
                                                'En Attente' => 'bg-yellow-100 text-yellow-800',
                                                'Confirmé' => 'bg-green-100 text-green-800',
                                                'En cours' => 'bg-blue-100 text-blue-800',
                                                'Terminé' => 'bg-gray-100 text-gray-800',
                                                'Annulé' => 'bg-red-100 text-red-800'
                                            ];
                                            $statusColor = $statusColors[$rdv->rdvConfirmer] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                            {{ $rdv->rdvConfirmer }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rdv->patient->Telephone1 ?? 'N/A' }}
                                    @if($rdv->patient->Telephone2)
                                        <br><span class="text-xs text-gray-500">{{ $rdv->patient->Telephone2 }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($rdv->patient && $rdv->patient->Telephone1)
                                        @php
                                            $isRelance = $rdv->rdvConfirmer === 'Rappel envoyé';
                                            $buttonText = $isRelance ? 'Relancer' : 'Rappeler';
                                            $buttonColor = $isRelance ? 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500';
                                        @endphp
                                        <button wire:click="sendReminder({{ $rdv->IDRdv }})"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="opacity-50 cursor-not-allowed"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white {{ $buttonColor }} focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                                            <i class="fab fa-whatsapp mr-1"></i>
                                            <span wire:loading.remove wire:target="sendReminder({{ $rdv->IDRdv }})">
                                                {{ $buttonText }}
                                            </span>
                                            <span wire:loading wire:target="sendReminder({{ $rdv->IDRdv }})" class="flex items-center">
                                                <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-white mr-1"></div>
                                                Envoi...
                                            </span>
                                        </button>
                                    @else
                                        <span class="text-red-600 text-xs">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Pas de téléphone
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center">
                <i class="fas fa-bell-slash text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 text-lg">Aucun rendez-vous à rappeler</p>
            </div>
        @endif

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $rendezVous->links() }}
        </div>
    </div>

</div>

<script>
    // Variable pour éviter les doublons
    let whatsappWindow = null;
    
    // Optimisation du chargement des événements
    document.addEventListener('DOMContentLoaded', function() {
    // Écouter l'événement d'ouverture de WhatsApp
    window.addEventListener('open-whatsapp-reminder', function(e) {
        if (e.detail && e.detail.url) {
            // Fermer l'onglet précédent s'il existe
            if (whatsappWindow && !whatsappWindow.closed) {
                whatsappWindow.close();
            }
            
            // Ouvrir WhatsApp dans un nouvel onglet
            whatsappWindow = window.open(e.detail.url, '_blank');
            
            // Afficher une notification (optionnel)
            if (e.detail.patientName) {
                console.log('Rappel envoyé pour:', e.detail.patientName, 'le', e.detail.rdvDate, 'à', e.detail.rdvTime);
            }
        }
    });
    });

    // Optimisation du debounce pour la recherche
    let searchTimeout;
    function debounceSearch(func, wait) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(func, wait);
    }
</script>
