<?php

namespace App\Http\Controllers\Client\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        try {
            $course = Course::where('lang', \App\Models\SettingLocal::getLang())->first();

            if ($course) {
                return response()->json([
                    'success' => true,
                    'data' => $course,
                    'array'=> false
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [],
                'array' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $course = Course::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $course
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function showPdf($id)
    {
        try {
            $course = Course::findOrFail($id);
            
            if (Storage::exists($course->pdf_path)) {
                $filePath = storage_path('app/' . $course->pdf_path);
                $contentType = mime_content_type($filePath);
                
                return response()->file($filePath, [
                    'Content-Type' => $contentType,
                    'X-Frame-Options' => "SAMEORIGIN",
                    'Access-Control-Allow-Origin' => "*"
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('No pdf file')
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 