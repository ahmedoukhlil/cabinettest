<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi File d'Attente - {{ $patient->Nom }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-en-attente { @apply bg-yellow-100 text-yellow-800; }
        .status-confirme { @apply bg-green-100 text-green-800; }
        .status-annule { @apply bg-red-100 text-red-800; }
        .status-termine { @apply bg-blue-100 text-blue-800; }
        .current-patient { @apply bg-blue-500 text-white; }
        .other-patient { @apply bg-gray-200 text-gray-700; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-6 max-w-4xl">
                 <!-- En-tête du cabinet -->
         <div class="bg-white rounded-lg shadow-md p-6 mb-6">
             @include('partials.recu-header')
         </div>
 
                  <!-- Informations principales -->
         <div class="bg-white rounded-lg shadow-md p-6 mb-6">
             <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                 <!-- Informations patient -->
                 <div>
                     <h2 class="text-xl font-bold text-gray-800 mb-4">
                         <i class="fas fa-user text-blue-600 mr-2"></i>
                         Informations Patient
                     </h2>
                     <div class="space-y-3">
                         <div>
                             <span class="text-sm font-medium text-gray-600">Nom :</span>
                             <span class="text-lg font-semibold text-gray-800">{{ $patient->Nom }}</span>
                         </div>
                         @if($prochainRdv)
                             <div>
                                 <span class="text-sm font-medium text-gray-600">Médecin :</span>
                                 <span class="text-lg font-semibold text-gray-800">Dr. {{ $prochainRdv->medecin->Nom ?? '' }} {{ $prochainRdv->medecin->Prenom ?? '' }}</span>
                             </div>
                             <div>
                                 <span class="text-sm font-medium text-gray-600">Date :</span>
                                 <span class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::parse($prochainRdv->dtPrevuRDV)->format('d/m/Y') }}</span>
                             </div>
                         @endif
                     </div>
                 </div>
 
                 <!-- Statut actuel -->
                 <div>
                     <h2 class="text-xl font-bold text-gray-800 mb-4">
                         <i class="fas fa-clock text-green-600 mr-2"></i>
                         Statut Actuel
                     </h2>
                     @if($prochainRdv && $positionPatient)
                         <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-4 text-white">
                             <div class="text-center">
                                 <div class="text-3xl font-bold mb-2">{{ $prochainRdv->ordreRDV ?? $positionPatient }}</div>
                                 <div class="text-sm opacity-90">Votre position dans la file</div>
                                 @if(($prochainRdv->ordreRDV ?? $positionPatient) > 1)
                                     <div class="text-sm opacity-90 mt-1">
                                         <i class="fas fa-users mr-1"></i>
                                         {{ ($prochainRdv->ordreRDV ?? $positionPatient) - 1 }} patient(s) avant vous
                                     </div>
                                 @endif
                                 @if($tempsAttenteEstime)
                                     <div class="text-sm opacity-90 mt-2">
                                         <i class="fas fa-hourglass-half mr-1"></i>
                                         ~{{ $tempsAttenteEstime }} min d'attente
                                     </div>
                                 @endif
                             </div>
                         </div>
                     @else
                         <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-4">
                             <div class="text-center">
                                 <i class="fas fa-info-circle text-yellow-600 text-2xl mb-2"></i>
                                 <div class="text-yellow-800 font-medium">Aucun rendez-vous aujourd'hui</div>
                             </div>
                         </div>
                     @endif
                 </div>
 
                 <!-- Patient en cours de traitement -->
                 @if($prochainRdv && $patientEnCours)
                     <div>
                         <h2 class="text-xl font-bold text-gray-800 mb-4">
                             <i class="fas fa-user-md text-green-600 mr-2"></i>
                             Patient en cours
                         </h2>
                         <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                             <div class="text-center">
                                 <div class="text-3xl font-bold mb-2">
                                     <i class="fas fa-user-md mr-2"></i>
                                     N° {{ $patientEnCours->OrdreRDV ?? $positionPatientEnCours ?? 'N/A' }}
                                 </div>
                                 <div class="text-sm opacity-90">Avec le médecin</div>
                                 <div class="text-xs opacity-75 mt-1">En cours de traitement</div>
                             </div>
                         </div>
                     </div>
                 @endif
             </div>
         </div>

        <!-- Patient en cours -->
        @if($prochainRdv && $patientEnCours)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-green-800">
                            <i class="fas fa-user-md mr-2"></i>
                            Patient en cours de traitement
                        </h3>
                                                 <p class="text-green-700">Numéro : <span class="font-bold">{{ $patientEnCours->ordreRDV ?? $positionPatientEnCours ?? 'N/A' }}</span></p>
                    </div>
                    <div class="text-right">
                                                 <div class="text-2xl font-bold text-green-600">
                             <i class="fas fa-user-md mr-2"></i>
                             Avec le médecin
                         </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Planning du médecin -->
        @if($prochainRdv && $rendezVousMedecinJournee->count() > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-calendar-day mr-2"></i>
                        Planning du Dr. {{ $prochainRdv->medecin->Nom ?? '' }} {{ $prochainRdv->medecin->Prenom ?? '' }}
                        <span class="text-sm font-normal text-gray-600">
                            - {{ \Carbon\Carbon::parse($prochainRdv->dtPrevuRDV)->format('d/m/Y') }}
                        </span>
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-sort-numeric-up mr-1"></i>N° RDV
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-clock mr-1"></i>Heure
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-info-circle mr-1"></i>Statut
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rendezVousMedecinJournee as $index => $rdv)
                                @php
                                    $isCurrentPatient = $rdv->fkidPatient == $patient->IDPatient;
                                    $isEnCours = $rdv->rdvConfirmer == 'En cours';
                                    $rowClass = $isCurrentPatient ? 'bg-blue-50 border-l-4 border-blue-500' : ($isEnCours ? 'bg-green-50 border-l-4 border-green-500' : 'hover:bg-gray-50');
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 {{ $isCurrentPatient ? 'current-patient' : ($isEnCours ? 'bg-green-500 text-white' : 'other-patient') }} text-sm font-bold rounded-full mr-3">
                                                {{ $rdv->OrdreRDV ?? ($index + 1) }}
                                            </span>
                                            @if($isCurrentPatient)
                                                <span class="text-sm text-blue-600 font-medium">Vous</span>
                                                                                         @elseif($isEnCours)
                                                 <span class="text-sm text-green-600 font-medium">Avec le médecin</span>
                                            @else
                                                <span class="text-sm text-gray-500">Patient masqué</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($rdv->HeureRdv)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Aucun planning disponible -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-calendar-day mr-2"></i>
                        Planning du médecin
                    </h2>
                </div>
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun planning disponible</h3>
                    <p class="text-gray-500">Aucun rendez-vous programmé pour aujourd'hui.</p>
                </div>
            </div>
        @endif

        <!-- Pied de page -->
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>Cette page se met à jour automatiquement toutes les 30 secondes.</p>
            <p class="mt-2">
                <i class="fas fa-shield-alt mr-1"></i>
                Vos données sont sécurisées et confidentielles
            </p>
        </div>
    </div>

    <!-- Auto-refresh script -->
    <script>
        // Actualiser la page toutes les 30 secondes
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html> 