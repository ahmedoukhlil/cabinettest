<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REÇU DE RENDEZ-VOUS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #fff; font-size: 12px; }
        .a4 { width: 210mm; min-height: 297mm; margin: auto; background: #fff; padding: 0 18mm 0 10mm; position: relative; box-sizing: border-box; display: flex; flex-direction: column; min-height: 297mm; }
        .a5 { width: 148mm; min-height: 210mm; margin: auto; background: #fff; padding: 0 10mm 0 5mm; position: relative; box-sizing: border-box; display: flex; flex-direction: column; min-height: 210mm; }
        .facture-title { text-align: center; font-size: 22px; font-weight: bold; margin-top: 10px; margin-bottom: 28px; letter-spacing: 2px; }
        .a5 .facture-title { font-size: 18px; margin-bottom: 20px; }
        .bloc-patient { margin: 0 0 10px 0; }
        .bloc-patient-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .bloc-patient-table td { padding: 2px 8px; font-size: 12px; }
        .a5 .bloc-patient-table td { font-size: 10px; padding: 1px 4px; }
        .bloc-patient-table .label { font-weight: bold; color: #222; width: 80px; }
        .bloc-patient-table .value { color: #222; }
        .bloc-patient-table .ref-cell { text-align: right; padding: 2px 4px; }
        .bloc-patient-table .ref-label { font-weight: bold; padding-right: 3px; display: inline; }
        .bloc-patient-table .ref-value { display: inline; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .details-table th, .details-table td { border: 1px solid #222; font-size: 12px; padding: 6px 8px; }
        .a5 .details-table th, .a5 .details-table td { font-size: 10px; padding: 4px 6px; }
        .details-table th { background: #f4f6fa; text-align: center; }
        .details-table td { text-align: center; }
        .details-table th:first-child, .details-table td:first-child { text-align: left; }
        .details-table th:last-child, .details-table td:last-child { width: 40%; text-align: left; }
        .totaux-table { width: 40%; border-collapse: collapse; margin-top: 0; margin-bottom: 0; margin-left: auto; }
        .totaux-table td { border: 1px solid #222; font-size: 12px; padding: 6px 8px; text-align: right; }
        .a5 .totaux-table td { font-size: 10px; padding: 4px 6px; }
        .montant-lettres { margin-top: 18px; font-size: 12px; clear: both; text-align: left; }
        .a5 .montant-lettres { font-size: 10px; margin-top: 12px; }
        .recu-header, .recu-footer { width: 100%; text-align: center; }
        .recu-header img, .recu-footer img { max-width: 100%; height: auto; }
        .recu-footer { position: absolute; bottom: 0; left: 0; width: 100%; }
        @media print { 
            .a4, .a5 { box-shadow: none; } 
            .recu-footer { position: fixed; bottom: 0; left: 0; width: 100%; } 
            .print-controls { display: none !important; }
            .qr-code-container:hover { transform: none; }
            .qr-code-link { color: #000 !important; }
            .qr-code-container { background: #fff !important; }
            a { color: #000 !important; text-decoration: none !important; }
        }
        .print-controls {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin: 18px 0;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .print-controls select,
        .print-controls button,
        .whatsapp-btn {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .print-btn {
            background: #2c5282;
            color: white;
            border: 1px solid #2c5282;
        }
        
        .print-btn:hover {
            background: #2a4a7c;
            border-color: #2a4a7c;
        }
        
        .whatsapp-btn {
            background: #25D366;
            color: white;
            border: 1px solid #25D366;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        
        .whatsapp-btn:hover {
            background: #128C7E;
            border-color: #128C7E;
        }
        
        .whatsapp-logo {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }
        
        /* Masquer les boutons WhatsApp à l'impression */
        @media print {
            .whatsapp-btn {
                display: none !important;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .print-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .print-controls select,
            .print-controls button,
            .whatsapp-btn {
                width: 100%;
                margin-bottom: 5px;
            }
        }
        .bloc-patient-table .praticien-value { padding-left: 2px !important; }
        .signature-block {
            margin-top: 40px;
            margin-bottom: 40px;
            text-align: right;
            padding-right: 20px;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 25px;
        }
        .signature-name {
            font-style: italic;
        }
        .qr-code-link {
            text-decoration: none;
            color: inherit;
            display: inline-block;
        }
        .qr-code-link:hover {
            transform: scale(1.02);
        }
        .qr-code-container {
            display: inline-block;
            padding: 4px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .qr-code-container:hover {
            transform: scale(1.02);
        }
        .qr-code-accessibility {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        .ordre-rdv {
            display: block;
            background: #2c5282;
            color: white;
            font-weight: bold;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 6px;
            border: 2px solid #1a365d;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            margin: 15px auto;
            text-align: center;
            width: fit-content;
            min-width: 80px;
        }
        .a5 .ordre-rdv {
            font-size: 12px;
            padding: 6px 12px;
            min-width: 70px;
        }
    </style>
</head>
<body>
<div class="a4" id="documentContainer">
    <div class="print-controls">
        <select id="pageFormat" onchange="updatePageFormat()">
            <option value="A4">Format A4</option>
            <option value="A5">Format A5</option>
        </select>
        <button onclick="window.print()" class="print-btn">
            Imprimer
        </button>
        
        <!-- BOUTON WHATSAPP -->
        @if($rendezVous->patient->Telephone1 && $rendezVous->patient->Telephone1 !== 'N/A')
            @php
                $telephoneNettoye = \App\Helpers\QrCodeHelper::formatPhoneForWhatsApp($rendezVous->patient->Telephone1);
            @endphp
            @if($telephoneNettoye)
                <button onclick="envoyerConfirmationRdvWhatsApp()" class="whatsapp-btn">
                    <svg class="whatsapp-logo" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                    Confirmation RDV
                </button>
            @endif
        @endif
    </div>
    <div class="recu-header">@include('partials.recu-header')</div>
    <div class="facture-title">REÇU DE RENDEZ-VOUS</div>
    
    @if($rendezVous->OrdreRDV)
        <div class="ordre-rdv">N° {{ str_pad($rendezVous->OrdreRDV, 3, '0', STR_PAD_LEFT) }}</div>
    @endif
    
    <div class="bloc-patient">
        <table class="bloc-patient-table">
            <tr>
                <td class="label">N° Fiche :</td>
                <td class="value">{{ $rendezVous->patient->IdentifiantPatient ?? 'N/A' }}</td>
                <td class="ref-cell" colspan="2">
                    <span class="ref-label">Réf :</span>
                    <span class="ref-value">{{ $rendezVous->IDRdv ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Nom Patient :</td>
                <td class="value">{{ $rendezVous->patient->NomContact ?? 'N/A' }}</td>
                <td class="ref-cell" colspan="2">
                    <span class="ref-label">Date :</span>
                    <span class="ref-value">{{ $rendezVous->dtPrevuRDV ? \Carbon\Carbon::parse($rendezVous->dtPrevuRDV)->format('d/m/Y') : 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Heure :</td>
                <td class="value">{{ $rendezVous->HeureRdv ? \Carbon\Carbon::parse($rendezVous->HeureRdv)->format('H:i') : 'N/A' }}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td class="label">Téléphone :</td>
                <td class="value">{{ $rendezVous->patient->Telephone1 ?? 'N/A' }}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td class="label">Praticien :</td>
                <td class="value">Dr. {{ $rendezVous->medecin->Nom ?? '' }} {{ $rendezVous->medecin->Prenom ?? '' }}</td>
                <td colspan="2"></td>
            </tr>
        </table>
    </div>
    <table class="details-table">
        <thead>
        <tr>
            <th>Acte prévu</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $rendezVous->ActePrevu ?? 'N/A' }}</td>
        </tr>
        </tbody>
    </table>

    <div class="signature-block">
        <div class="signature-title">Signature</div>
        <div class="signature-name">{{ $rendezVous->medecin->Nom ?? 'Non spécifié' }}</div>
    </div>

         <!-- QR Code pour l'interface patient - Positionné en bas à gauche -->
     <div style="position: fixed; bottom: 120px; left: 20px; z-index: 1000;">
         <div style="text-align: center;">
             @php
                 try {
                     $token = App\Http\Controllers\PatientInterfaceController::generateToken($rendezVous->fkidPatient, $rendezVous->dtPrevuRDV);
                     $patientUrl = route('patient.rendez-vous', ['token' => $token]);
                 } catch (Exception $e) {
                     $patientUrl = '#';
                 }
             @endphp
             <a href="{{ $patientUrl }}" target="_blank" class="qr-code-link" aria-label="Ouvrir l'interface patient pour suivre votre file d'attente">
                 <div class="qr-code-container">
                     <div style="max-width: 100px; height: auto;">
                         {!! App\Helpers\QrCodeHelper::generateRendezVousQrCode($rendezVous->fkidPatient) !!}
                     </div>
                 </div>
             </a>
             <div style="margin-top: 4px; font-size: 8px; color: #333; font-weight: 600;">
                 Suivez votre file d'attente
             </div>
         </div>
     </div>

    <div class="recu-footer">@include('partials.recu-footer')</div>
</div>

<script>
// Cache des éléments DOM fréquemment utilisés
const elements = {
    pageFormat: document.getElementById('pageFormat'),
    container: document.getElementById('documentContainer')
};

function updatePageFormat() {
    const isA5 = elements.pageFormat.value === 'A5';
    elements.container.classList.toggle('a4', !isA5);
    elements.container.classList.toggle('a5', isA5);
}

// FONCTIONS WHATSAPP POUR RENDEZ-VOUS
function envoyerConfirmationRdvWhatsApp() {
    if (!verifierTelephoneRdv()) {
        return;
    }
    
    // Données du rendez-vous
    const nomPatient = "{{ $rendezVous->patient->NomContact }}";
    const telephone = "{{ $telephoneNettoye ?? '' }}";
    const dateRdv = "{{ \Carbon\Carbon::parse($rendezVous->dtPrevuRDV)->format('d/m/Y') }}";
    
    // Gestion de l'heure avec debug amélioré
    let heureRdv = '';
    
    // Debug des valeurs brutes
    console.log('HeureRdv brute:', "{{ $rendezVous->HeureRdv ?? 'null' }}");
    console.log('Type HeureRdv:', typeof("{{ $rendezVous->HeureRdv ?? 'null' }}"));
    
    @php
        $heureRdv = $rendezVous->HeureRdv;
        $heureFormatee = '';
        
        if ($heureRdv) {
            try {
                // Si c'est déjà un objet Carbon
                if ($heureRdv instanceof \Carbon\Carbon) {
                    $heureFormatee = $heureRdv->format('H:i');
                } else {
                    // Essayer de parser la valeur
                    $carbon = \Carbon\Carbon::parse($heureRdv);
                    $heureFormatee = $carbon->format('H:i');
                }
            } catch (Exception $e) {
                // Si le parsing échoue, utiliser la valeur brute
                $heureFormatee = $heureRdv;
            }
        }
    @endphp
    
    heureRdv = "{{ $heureFormatee ?: 'À définir' }}";
    
    const medecin = "{{ trim(($rendezVous->medecin->Nom ?? '') . ' ' . ($rendezVous->medecin->Prenom ?? '')) ?: 'Médecin non défini' }}";
    
    // Gestion du numéro d'ordre
    let ordreRdv = '';
    @if($rendezVous->OrdreRDV && $rendezVous->OrdreRDV > 0)
        ordreRdv = "{{ str_pad($rendezVous->OrdreRDV, 3, '0', STR_PAD_LEFT) }}";
    @else
        ordreRdv = "À définir";
    @endif
    
    // Debug complet
    console.log('=== DEBUG RENDEZ-VOUS ===');
    console.log('Heure brute:', "{{ $rendezVous->HeureRdv ?? 'null' }}", '-> Formatée:', heureRdv);
    console.log('Ordre brut:', "{{ $rendezVous->OrdreRDV ?? 'null' }}", '-> Formaté:', ordreRdv);
    console.log('Médecin:', medecin);
    console.log('========================');
    
    @php
        try {
            $token = App\Http\Controllers\PatientInterfaceController::generateToken($rendezVous->fkidPatient, $rendezVous->dtPrevuRDV);
            $patientUrl = route('patient.rendez-vous', ['token' => $token]);
        } catch (Exception $e) {
            $patientUrl = url('/');
        }
    @endphp
    
    const lienSuivi = "{{ $patientUrl }}";
    
    // Nettoyer le téléphone
    const phoneClean = telephone.replace(/[\s\-\(\)]/g, '');
    
    // Message bilingue
    const message = construireMessageRdvBilingue(nomPatient, dateRdv, heureRdv, medecin, ordreRdv, lienSuivi);
    
    // Créer le lien WhatsApp Web
    const whatsappWebUrl = `https://wa.me/${phoneClean}?text=${encodeURIComponent(message)}`;
    
    // Ouvrir WhatsApp Web dans un nouvel onglet
    window.open(whatsappWebUrl, '_blank');
    
    // Notification de succès
    mostrarNotificationSuccesRdv();
}

function construireMessageRdvBilingue(nom, date, heure, medecin, ordre, lienSuivi) {
    // Debug des paramètres reçus
    console.log('=== CONSTRUCTION MESSAGE ===');
    console.log('Nom:', nom);
    console.log('Date:', date);
    console.log('Heure reçue:', heure);
    console.log('Médecin:', medecin);
    console.log('Ordre reçu:', ordre);
    console.log('Lien:', lienSuivi);
    console.log('============================');
    
    // Construction du message avec formatage simple compatible WhatsApp
    const message = `*تم تأكيد الموعد*
*Rendez-vous confirmé*

*${nom || 'Nom non défini'}*

التاريخ: ${date || 'تاريخ غير محدد'}
الوقت: ${heure || 'وقت غير محدد'}
الطبيب: د. ${medecin || 'طبيب غير محدد'}
الرقم: ${ordre || 'رقم غير محدد'}

━━━━━━━━━━━━━━━━━━━━━━

Date: ${date || 'Date non définie'}
Heure: ${heure || 'Heure non définie'}
Médecin: Dr. ${medecin || 'Médecin non défini'}
Numéro: ${ordre || 'Numéro non défini'}

*رابط متابعة طابور الانتظار:*
*Lien de suivi de la file d'attente:*
${lienSuivi || 'Lien non disponible'}

استخدم هذا الرابط لمعرفة رقمك في الطابور ووقت الانتظار المتوقع
Utilisez ce lien pour connaître votre numéro dans la file et le temps d'attente estimé

شكراً - Merci`;

    // Debug du message final
    console.log('=== MESSAGE FINAL ===');
    console.log(message);
    console.log('====================');
    
    return message;
}

// Vérification du numéro de téléphone pour RDV
function verifierTelephoneRdv() {
    const telephone = "{{ $telephoneNettoye ?? '' }}";
    
    if (!telephone || telephone.trim() === '' || telephone === 'N/A') {
        alert('❌ Aucun numéro de téléphone disponible pour ce patient.\n\nVeuillez ajouter un numéro dans la fiche patient.');
        return false;
    }
    
    const phoneClean = telephone.replace(/[\s\-\(\)]/g, '');
    if (phoneClean.length < 8) {
        alert('❌ Le numéro de téléphone semble invalide.\n\nVeuillez vérifier le numéro dans la fiche patient.');
        return false;
    }
    
    return true;
}

function mostrarNotificationSuccesRdv() {
    const message = 'Message de confirmation RDV envoyé vers WhatsApp !';
    
    // Créer une notification temporaire
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #25D366;
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 10000;
        font-weight: bold;
        animation: slideInRdv 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Supprimer après 3 secondes
    setTimeout(() => {
        notification.style.animation = 'slideOutRdv 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Ajouter les animations CSS pour RDV
const styleRdv = document.createElement('style');
styleRdv.textContent = `
    @keyframes slideInRdv {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRdv {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(styleRdv);
</script>
</body>
</html> 