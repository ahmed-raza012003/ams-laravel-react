<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ExportService
{
    /**
     * Get company information from config
     */
    public static function getCompanyInfo(): array
    {
        return [
            'name' => config('app.company_name', 'FinanceFlow'),
            'logo' => self::getCompanyLogo(),
            'address' => config('app.company_address'),
            'phone' => config('app.company_phone'),
            'email' => config('app.company_email'),
            'website' => config('app.company_website'),
        ];
    }

    /**
     * Get company logo path or base64 data
     */
    public static function getCompanyLogo(): ?string
    {
        $logoPath = config('app.company_logo');
        
        if (!$logoPath) {
            return null;
        }

        // If it's an absolute path, check if file exists
        if (file_exists($logoPath)) {
            $imageData = file_get_contents($logoPath);
            $imageType = pathinfo($logoPath, PATHINFO_EXTENSION);
            return 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
        }

        // If it's a storage path
        if (Storage::exists($logoPath)) {
            $imageData = Storage::get($logoPath);
            $imageType = pathinfo($logoPath, PATHINFO_EXTENSION);
            return 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
        }

        // If it's a public path
        $publicPath = public_path($logoPath);
        if (file_exists($publicPath)) {
            $imageData = file_get_contents($publicPath);
            $imageType = pathinfo($logoPath, PATHINFO_EXTENSION);
            return 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
        }

        return null;
    }

    /**
     * Format currency amount
     */
    public static function formatCurrency($amount, $currency = '£'): string
    {
        return $currency . number_format((float) $amount, 2, '.', ',');
    }

    /**
     * Format date
     */
    public static function formatDate($date, $format = 'd/m/Y'): string
    {
        if (!$date) {
            return '';
        }
        
        try {
            return date($format, strtotime($date));
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Generate safe filename
     */
    public static function generateFilename($prefix, $identifier = null, $extension = 'pdf'): string
    {
        $timestamp = date('Y-m-d');
        $identifier = $identifier ? '-' . $identifier : '';
        return $prefix . $identifier . '-' . $timestamp . '.' . $extension;
    }
}

