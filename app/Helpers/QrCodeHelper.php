<?php

namespace App\Helpers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\PatientInterfaceController;

class QrCodeHelper
{
    /**
     * Génère un QR code pour l'interface patient des rendez-vous
     */
    public static function generateRendezVousQrCode($patientId)
    {
        $token = PatientInterfaceController::generateToken($patientId);
        $url = route('patient.rendez-vous', ['token' => $token]);
        
        return QrCode::format('svg')
            ->size(120)
            ->margin(0)
            ->errorCorrection('M')
            ->generate($url);
    }

    /**
     * Génère un QR code pour l'interface patient des consultations
     */
    public static function generateConsultationQrCode($patientId)
    {
        $token = PatientInterfaceController::generateToken($patientId);
        $url = route('patient.consultation', ['token' => $token]);
        
        return QrCode::format('svg')
            ->size(120)
            ->margin(0)
            ->errorCorrection('M')
            ->generate($url);
    }

    /**
     * Génère un QR code avec des paramètres personnalisés
     */
    public static function generateCustomQrCode($data, $size = 120, $format = 'svg')
    {
        return QrCode::format($format)
            ->size($size)
            ->margin(0)
            ->errorCorrection('M')
            ->generate($data);
    }
} 