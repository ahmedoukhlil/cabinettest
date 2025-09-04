<div class="space-y-6 max-w-3xl mx-auto p-4">
    <!-- En-tête avec recherche et boutons -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex-1 max-w-lg">
            <div class="relative">
                <div class="flex space-x-2">
                    <select wire:model="searchBy" class="rounded-l-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="all">Tout</option>
                        <option value="name">Nom/Prénom</option>
                        <option value="nni">NNI</option>
                        <option value="phone">Téléphone</option>
                    </select>
                    <input type="text" wire:model.debounce.300ms="search" placeholder="Rechercher un patient..." class="flex-1 pl-4 pr-4 py-2 rounded-r-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <label class="flex items-center space-x-2 text-sm text-gray-600">
                <input type="checkbox" wire:model="showInactive" class="form-checkbox h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                <span>Afficher les patients inactifs</span>
            </label>
            <button wire:click="openModal" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-lg font-semibold text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200">
                Nouveau Patient
            </button>
        </div>
    </div>

    <!-- Messages de notification -->
    @if (session()->has('message'))
        <div class="rounded-lg bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Tableau des patients -->
    <div class="bg-white rounded-lg shadow overflow-hidden w-full">
        <div>
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 py-2 w-1/12 text-center">N° Fiche</th>
                        <th scope="col" class="px-3 py-2 w-1/4">Nom</th>
                        <th scope="col" class="px-3 py-2 w-1/6">Téléphone</th>
                        <th scope="col" class="px-3 py-2 w-1/12 text-center">Genre</th>
                        <th scope="col" class="px-3 py-2 w-1/4">Assurance</th>
                        <th scope="col" class="px-3 py-2 w-1/12 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 w-1/12 text-center">{{ $patient->IdentifiantPatient }}</td>
                            <td class="px-3 py-2 w-1/4">{{ $patient->Prenom }}</td>
                            <td class="px-3 py-2 w-1/6">{{ $patient->Telephone1 }}@if($patient->Telephone2)<div class="text-xs text-gray-500">{{ $patient->Telephone2 }}</div>@endif</td>
                            <td class="px-3 py-2 w-1/12 text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ in_array($patient->Genre, ['H','M']) ? 'bg-blue-100 text-blue-800' : ($patient->Genre === 'F' ? 'bg-pink-100 text-pink-800' : 'bg-gray-100 text-gray-500') }}">
                                    @if(in_array($patient->Genre, ['H','M']))
                                        H
                                    @elseif($patient->Genre === 'F')
                                        F
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td class="px-3 py-2 w-1/4">
                                @if($patient->Assureur > 0 && $patient->assureur)
                                    <div class="space-y-1">
                                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200 shadow-sm">
                                            {{ $patient->assureur->LibAssurance }}
                                        </span>
                                        <div class="text-xs text-gray-500">
                                            Taux PEC: {{ number_format($patient->assureur->TauxdePEC, 2) }}%
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500 border border-gray-200 italic">
                                        Non assuré
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 w-1/12 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <button wire:click="showPaymentHistory({{ $patient->ID }})" class="text-green-600 hover:text-green-900" title="Historique des paiements">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                    <button wire:click="toggleStatus({{ $patient->ID }})" class="text-{{ $patient->choix ? 'green' : 'red' }}-600 hover:text-{{ $patient->choix ? 'green' : 'red' }}-900" title="{{ $patient->choix ? 'Activer' : 'Désactiver' }}">
                                        <i class="fas fa-{{ $patient->choix ? 'check' : 'times' }}"></i>
                                    </button>
                                    <button wire:click="edit({{ $patient->ID }})" class="text-primary hover:text-primary-dark" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-center">
                                Aucun patient trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $patients->links() }}
        </div>
    </div>

    <!-- Modal de création/édition -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop animate-backdrop-fade-in p-2 sm:p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full h-full max-w-4xl max-h-[95vh] p-0 relative modal-container animate-modal-fade-in flex flex-col">
            <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 rounded-t-2xl modal-header bg-gradient-to-r from-blue-600 to-blue-700 text-white flex-shrink-0">
                <div class="flex items-center gap-2 sm:gap-3">
                    <i class="fas fa-user-plus header-icon text-white text-lg sm:text-xl"></i>
                    <h2 class="text-lg sm:text-xl font-bold text-white">{{ $patientId ? 'Modifier le patient' : 'Nouveau patient' }}</h2>
                </div>
                <button wire:click="closeModal" 
                        class="modal-close-button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="flex-1 overflow-hidden">
                <form wire:submit.prevent="save" class="h-full flex flex-col">
                    <div class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6 modal-body animate-modal-content-slide-in">
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Informations personnelles -->
                            <div class="bg-gray-50 p-4 rounded-lg animate-speed-fade-in" style="animation-delay: 0.1s;">
                                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <i class="fas fa-user mr-2 text-blue-600"></i>
                                    Informations personnelles
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="identifiantPatient" class="block text-sm font-medium text-gray-700">N° Fiche</label>
                                        <input type="number" wire:model.defer="identifiantPatient" id="identifiantPatient" class="modal-form-input" @if(!$patientId) disabled @endif>
                                        @error('identifiantPatient') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        @if(!$patientId)
                                            <p class="text-xs text-gray-500">Le N° Fiche est généré automatiquement à la création.</p>
                                        @endif
                                    </div>
                                    <div>
                                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom *</label>
                                        <input type="text" wire:model.defer="nom" id="nom" class="modal-form-input" placeholder="Entrez le nom du patient">
                                        @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="nni" class="block text-sm font-medium text-gray-700">NNI</label>
                                        <input type="text" wire:model.defer="nni" id="nni" class="modal-form-input" placeholder="Numéro d'identité national">
                                        @error('nni') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="genre" class="block text-sm font-medium text-gray-700">Genre</label>
                                        <select wire:model.defer="genre" id="genre" class="modal-form-input">
                                            <option value="">Sélectionner</option>
                                            <option value="H">Homme (H)</option>
                                            <option value="F">Femme (F)</option>
                                        </select>
                                        @error('genre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="dateNaissance" class="block text-sm font-medium text-gray-700">Date de naissance</label>
                                        <input type="date" wire:model.defer="dateNaissance" id="dateNaissance" class="modal-form-input">
                                        @error('dateNaissance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="telephone1" class="block text-sm font-medium text-gray-700">Téléphone principal *</label>
                                        <input type="tel" wire:model.defer="telephone1" id="telephone1" class="modal-form-input" placeholder="Ex: +222 12345678">
                                        @error('telephone1') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="telephone2" class="block text-sm font-medium text-gray-700">Téléphone secondaire</label>
                                        <input type="tel" wire:model.defer="telephone2" id="telephone2" class="modal-form-input" placeholder="Ex: +222 12345678">
                                        @error('telephone2') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                                        <textarea wire:model.defer="adresse" id="adresse" rows="2" class="modal-form-input" placeholder="Adresse complète du patient"></textarea>
                                        @error('adresse') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" wire:model="isAssured" class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">Patient assuré</span>
                                    </label>
                                </div>

                                @if($isAssured)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 animate-speed-fade-in" style="animation-delay: 0.2s;">
                                    <div>
                                        <label for="assureur" class="block text-sm font-medium text-gray-700">Assureur *</label>
                                        <select wire:model="assureur" id="assureur" class="modal-form-input">
                                            <option value="">Sélectionner un assureur</option>
                                            @foreach($assureurs as $assureur)
                                                <option value="{{ $assureur->IDAssureur }}">{{ $assureur->LibAssurance }}</option>
                                            @endforeach
                                        </select>
                                        @error('assureur') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="identifiantAssurance" class="block text-sm font-medium text-gray-700">Identifiant Assurance *</label>
                                        <input type="text" wire:model="identifiantAssurance" id="identifiantAssurance" class="modal-form-input" placeholder="Numéro d'assuré">
                                        @error('identifiantAssurance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Statut du patient -->
                            <div class="bg-gray-50 p-4 rounded-lg animate-speed-fade-in" style="animation-delay: 0.3s;">
                                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <i class="fas fa-toggle-on mr-2 text-blue-600"></i>
                                    Statut du patient
                                </h4>
                                <div>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" wire:model.defer="isActive" class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">Patient actif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" 
                                wire:click="closeModal"
                                class="modal-btn modal-btn-secondary">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </button>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="modal-btn modal-btn-primary">
                            <span wire:loading.remove wire:target="save">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center">
                                <div class="animate-speed-loading rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                Enregistrement...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal d'historique des paiements -->
    @if($showPaymentHistoryModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop animate-backdrop-fade-in p-2 sm:p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full h-full max-w-6xl max-h-[95vh] p-0 relative modal-container animate-modal-fade-in flex flex-col">
            <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 rounded-t-2xl modal-header bg-gradient-to-r from-blue-600 to-blue-700 text-white flex-shrink-0">
                <div class="flex items-center gap-2 sm:gap-3">
                    <i class="fas fa-money-bill-wave header-icon text-white text-lg sm:text-xl"></i>
                    <h2 class="text-lg sm:text-xl font-bold text-white">Historique des paiements</h2>
                </div>
                <button wire:click="closePaymentHistoryModal" 
                        class="modal-close-button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="flex-1 overflow-hidden">
                <div class="h-full overflow-y-auto p-3 sm:p-4 md:p-6 modal-body animate-modal-content-slide-in">
                    @if($paymentHistory->isEmpty())
                        <div class="text-center py-8 animate-speed-fade-in" style="animation-delay: 0.1s;">
                            <i class="fas fa-info-circle text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500 text-lg">Aucun paiement enregistré pour ce patient</p>
                        </div>
                    @else
                        <div class="overflow-x-auto animate-speed-fade-in" style="animation-delay: 0.1s;">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médecin</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($paymentHistory as $index => $payment)
                                        <tr class="animate-speed-slide-in" style="animation-delay: {{ 0.2 + ($index * 0.05) }}s;">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($payment->dateoper)->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $payment->designation }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                                {{ number_format($payment->MontantOperation, 0, ',', ' ') }} MRU
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->entreEspece ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} animate-speed-pulse">
                                                    {{ $payment->entreEspece ? 'Entrée' : 'Sortie' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $payment->medecin }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                
                <div class="modal-footer">
                    <button type="button" 
                            wire:click="closePaymentHistoryModal" 
                            class="modal-btn modal-btn-secondary">
                        <i class="fas fa-times mr-2"></i>
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div> 