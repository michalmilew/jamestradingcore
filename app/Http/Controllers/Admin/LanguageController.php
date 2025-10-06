<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;

class LanguageController extends Controller
{
    public function index()
    {
        $locales = ['de', 'en', 'es', 'fr', 'it', 'nl', 'pt'];
        return view('admin.languages.index', compact('locales'));
    }

    public function edit(Request $request, $locale = 'en')
    {
        $locales = ['de', 'en', 'es', 'fr', 'it', 'nl', 'pt'];

        if ($request->has('language')) {
            $locale = $request->input('language');
        }

        $jsonFilePath = resource_path("lang/{$locale}.json");
        $phpLangPath = resource_path("lang/{$locale}/");

        $translations = [];

        // ✅ Load JSON translations properly
        if (File::exists($jsonFilePath)) {
            $jsonData = json_decode(File::get($jsonFilePath), true);
            if (is_array($jsonData)) {
                $translations['json']['json'] = $jsonData; // Store under ['json']['json'] for consistency
            } else {
                $translations['json']['json'] = []; // Prevent errors when JSON file is empty or invalid
            }
        }

        // ✅ Load PHP translations correctly
        if (File::exists($phpLangPath) && is_dir($phpLangPath)) {
            foreach (File::allFiles($phpLangPath) as $file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $phpData = include $file;

                if (is_array($phpData)) {
                    $translations['php'][$filename] = $phpData;
                } else {
                    $translations['php'][$filename] = []; // Prevent errors if the PHP file is not an array
                }
            }
        }

        // ✅ Apply search filter
        // Apply search filter if provided
        if ($request->has('search') && trim($request->input('search')) !== '') {
            $search = trim($request->input('search'));

            foreach ($translations as $type => $files) {
                foreach ($files as $fileName => $data) {
                    if (is_array($data)) {
                        // Flatten the array to allow searching in nested PHP files
                        $flattenedData = [];
                        array_walk_recursive($data, function ($value, $key) use (&$flattenedData) {
                            if (is_string($value)) {
                                $flattenedData[$key] = $value;
                            }
                        });

                        // Filter only string values, skip nested arrays
                        $filteredData = array_filter($flattenedData, function ($value, $key) use ($search) {
                            return stripos($key, $search) !== false || stripos($value, $search) !== false;
                        }, ARRAY_FILTER_USE_BOTH);

                        // ✅ Update translations only if matches exist
                        if (!empty($filteredData)) {
                            $translations[$type][$fileName] = $filteredData;
                        } else {
                            unset($translations[$type][$fileName]); // Remove empty results
                        }
                    }
                }
            }
        }


        // ✅ Flatten translations for pagination
        $flatTranslations = [];
        foreach ($translations as $type => $files) {
            foreach ($files as $fileName => $data) {
                foreach ($data as $key => $value) {
                    $flatTranslations["{$type}.{$fileName}.{$key}"] = $value;
                }
            }
        }

        // ✅ Ensure pagination works correctly
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20; // Number of items per page
        $currentPageItems = array_slice($flatTranslations, ($currentPage - 1) * $perPage, $perPage, true);

        $paginator = new LengthAwarePaginator(
            $currentPageItems,
            count($flatTranslations),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.languages.edit', compact('locale', 'paginator', 'locales', 'translations'));
    }


    public function update(Request $request, $locale)
    {
        $jsonFilePath = resource_path("lang/{$locale}.json");
        $phpLangPath = resource_path("lang/{$locale}/");

        // Initialize an empty array for JSON translations
        $jsonTranslations = File::exists($jsonFilePath) ? json_decode(File::get($jsonFilePath), true) : [];

        // ✅ Update JSON translations
        if ($request->has('translations.json.json')) {
            foreach ($request->input('translations.json.json') as $key => $value) {
                $jsonTranslations[$key] = $value;
            }
            File::put($jsonFilePath, json_encode($jsonTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        // ✅ Update PHP translations
        if (File::exists($phpLangPath) && is_dir($phpLangPath)) {
            foreach (File::allFiles($phpLangPath) as $file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $phpTranslations = include $file;

                if (is_array($phpTranslations) && $request->has("translations.php.{$filename}")) {
                    foreach ($request->input("translations.php.{$filename}") as $key => $value) {
                        $phpTranslations[$key] = $value;
                    }
                }

                // Convert the updated PHP array back to a file
                $phpContent = "<?php\n\nreturn " . var_export($phpTranslations, true) . ";\n";
                File::put($file, $phpContent);
            }
        }

        return redirect()->back()->with('success', "Language files for {$locale} updated successfully.");
    }
}
