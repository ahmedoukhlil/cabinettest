<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REÇU DE CONSULTATION</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .print-controls { display: flex; gap: 10px; justify-content: flex-end; margin: 18px 0; }
        .print-controls select, .print-controls button { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .print-controls button { background: #2c5282; color: #fff; border: none; cursor: pointer; }
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
    </div>
    <div class="recu-header">@include('partials.recu-header')</div>
    <div class="facture-title">REÇU DE CONSULTATION</div>
    
    @if($facture->rendezVous && $facture->rendezVous->OrdreRDV)
        <div class="ordre-rdv">N° {{ str_pad($facture->rendezVous->OrdreRDV, 3, '0', STR_PAD_LEFT) }}</div>
    @endif
    
    <div class="bloc-patient">
        <table class="bloc-patient-table">
            <tr>
                <td class="label">N° Fiche :</td>
                <td class="value">{{ $facture->patient->IdentifiantPatient ?? 'N/A' }}</td>
                <td class="ref-cell" colspan="2">
                    <span class="ref-label">Réf :</span>
                    <span class="ref-value">{{ $facture->Nfacture ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Nom Patient :</td>
                <td class="value">{{ $facture->patient->NomContact ?? 'N/A' }}</td>
                <td class="ref-cell" colspan="2">
                    <span class="ref-label">Date :</span>
                    <span class="ref-value">{{ $facture->DtFacture ? \Carbon\Carbon::parse($facture->DtFacture)->format('d/m/Y') : 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Téléphone :</td>
                <td class="value">{{ $facture->patient->Telephone1 ?? 'N/A' }}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td class="label">Praticien :</td>
                <td class="value">{{ $facture->medecin->Nom ?? '' }} {{ $facture->medecin->Prenom ?? '' }}</td>
                <td colspan="2"></td>
            </tr>
        </table>
    </div>
    <table class="details-table">
        <thead>
        <tr>
            <th>Acte</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
            @foreach($facture->details as $detail)
                <tr>
                    <td>{{ $detail->Actes ?? 'N/A' }}</td>
                    <td>{{ $detail->Quantite ?? 1 }}</td>
                    <td>{{ number_format($detail->PrixFacture ?? 0, 2) }} MRU</td>
                    <td>{{ number_format(($detail->PrixFacture ?? 0) * ($detail->Quantite ?? 1), 2) }} MRU</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table class="totaux-table">
        <tr>
            <td>Total consultation</td>
            <td>{{ number_format($facture->TotFacture ?? 0, 2) }} MRU</td>
        </tr>
        @if(($facture->TotalPEC ?? 0) > 0)
            <tr>
                <td>Prise en charge</td>
                <td>{{ number_format($facture->TotalPEC ?? 0, 2) }} MRU</td>
            </tr>
            <tr>
                <td>Reste à payer</td>
                <td>{{ number_format($facture->TotalfactPatient ?? 0, 2) }} MRU</td>
            </tr>
        @endif
    </table>
    <div class="montant-lettres">
        Arrêté la présente consultation à la somme de : <strong>{{ $facture->en_lettres ?? '' }}</strong>
    </div>

    <div class="signature-block">
        <div class="signature-title">Signature</div>
        <div class="signature-name">Dr. {{ $facture->medecin->Nom ?? 'Non spécifié' }}</div>
    </div>

         <!-- QR Code pour l'interface patient - Positionné en bas à gauche -->
     <div style="position: fixed; bottom: 120px; left: 20px; z-index: 1000;">
         <div style="text-align: center;">
             @php
                 try {
                     $token = App\Http\Controllers\PatientInterfaceController::generateToken($facture->IDPatient);
                     $patientUrl = route('patient.rendez-vous', ['token' => $token]);
                 } catch (Exception $e) {
                     $patientUrl = '#';
                 }
             @endphp
             <a href="{{ $patientUrl }}" target="_blank" class="qr-code-link" aria-label="Ouvrir l'interface patient pour suivre votre file d'attente">
                 <div class="qr-code-container">
                     <div style="max-width: 100px; height: auto;">
                         @php
                             try {
                                 $qrCode = App\Helpers\QrCodeHelper::generateRendezVousQrCode($facture->IDPatient);
                                 echo $qrCode;
                             } catch (Exception $e) {
                                 echo '<div style="width: 100px; height: 100px; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #666; border-radius: 4px;">QR Code<br>Non disponible</div>';
                             }
                         @endphp
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
</script>
</body>
</html> 