<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Course;

class CourseController extends Controller
{
    //
    public function index(){
        $course = Course::where('lang', \App\Models\SettingLocal::getLang())->first();

        if($course != null){
            return View('client.courses.show', compact('course'));
        }else{
            $courses = collect([]);
            return View('client.courses.list', compact('courses'));
        }
    }

    public function show($id){
        $course = Course::find($id);
        return View('client.courses.show', compact('course'));
    }

    public function showpdf($id){
        $course = Course::find($id);
        if(Storage::exists($course->pdf_path)){
            $filePath = storage_path('app/' . $course->pdf_path);

            // Get the content type of the PDF
            $contentType = mime_content_type($filePath);

            // Return the PDF file as a response
            $response = response()->file($filePath, ['Content-Type' => $contentType,'X-Frame-Options' => "SAMEORIGIN",'Access-Control-Allow-Origin' => "*",]);
            return $response;
        }else{
            return __('No pdf file');
        }
    }
}
