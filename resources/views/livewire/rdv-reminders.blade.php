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
                <span class="inline-flex items-center justify-center px-2 py-1 rounded-full bg-white text-primary font-bold text-sm shadow">
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
        <div class="overflow-x-auto">
            @if($rendezVous->count() > 0)
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
                                    @php
                                        $statusColors = [
                                            'En Attente' => 'bg-yellow-100 text-yellow-800',
                                            'Confirmé' => 'bg-green-100 text-green-800',
                                            'En cours' => 'bg-blue-100 text-blue-800',
                                            'Rappel envoyé' => 'bg-orange-100 text-orange-800',
                                            'Terminé' => 'bg-gray-100 text-gray-800',
                                            'Annulé' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusColor = $statusColors[$rdv->rdvConfirmer] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $rdv->rdvConfirmer }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rdv->patient->Telephone1 ?? 'N/A' }}
                                    @if($rdv->patient->Telephone2)
                                        <br><span class="text-xs text-gray-500">{{ $rdv->patient->Telephone2 }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($rdv->patient && $rdv->patient->Telephone1)
                                        <button wire:click="sendReminder({{ $rdv->IDRdv }})"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="opacity-50 cursor-not-allowed"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                            <i class="fab fa-whatsapp mr-1"></i>
                                            <span wire:loading.remove wire:target="sendReminder({{ $rdv->IDRdv }})">
                                                Rappeler
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
            @else
                <tr>
                    <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        Aucun rendez-vous à rappeler
                    </td>
                </tr>
            @endif
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $rendezVous->links() }}
        </div>
    </div>


</div>

<script>
    // Variable pour éviter les doublons
    let whatsappWindow = null;
    
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
</script>
