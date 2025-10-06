<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

class TranslationController extends Controller
{
    public function index($locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'es', 'fr', 'de', 'it', 'nl', 'pt'])) {
            return response()->json(['error' => 'Invalid locale'], 400);
        }

        // Set the application locale
        app()->setLocale($locale);

        // Get translations from PHP files
        $translations = [
            'auth' => Lang::get('auth'),
            'dashboard' => Lang::get('dashboard'),
            'language' => Lang::get('language'),
            'notifications' => Lang::get('notifications'),
            'pagination' => Lang::get('pagination'),
            'passwords' => Lang::get('passwords'),
            'validation' => Lang::get('validation'),
        ];

        // Get JSON translations if they exist and merge them directly
        $jsonPath = lang_path("{$locale}.json");
        if (File::exists($jsonPath)) {
            $jsonTranslations = json_decode(File::get($jsonPath), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $translations = array_merge($translations, $jsonTranslations);
            }
        }

        return response()->json($translations);
    }
} 