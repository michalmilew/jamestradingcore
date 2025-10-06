<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Course;

class CourseController extends Controller
{
    //
    public function index(){
        $courses = Course::paginate(10);

        return View('courses.list', compact('courses'));
    }

    public function create(){
        return View('courses.create');
    }

    public function store(Request $request){
        //
        $validatedData = $request->validate([
            'name' => 'required',
            'url' => 'url',
            'lang' => 'required',
            //'pdf_file' => 'required|mimes:pdf|max:5000',
        ]);

        
        // if ($request->hasFile('pdf_file')) {
        //     $path = $request->file('pdf_file')->store('pdf_files');
    
        //     // Create a new course
        //     $course = new Course();
        //     $course->name = $request->name;
        //     $course->pdf_path = $path;
        //     $course->save();
    
        //     return redirect()->route(\App\Models\SettingLocal::getLang().'.admin.courses.index')->with('success', __('Course added by succesfully'));        
        // }

            // Create a new course
            $course = new Course();
            $course->name = $request->name;
            $course->url = $request->url;
            $course->lang = $request->lang;
            $course->save();
        return redirect()->route(\App\Models\SettingLocal::getLang().'.admin.courses.index')->with('success', __('Course added by succesfully'));        
        
        //return redirect()->back()->with('error', __('No attached document'));
    }

    public function edit($id){
        $course = Course::find($id);
        return View('courses.edit', compact('course'));
    }

    public function show($id){
        $course = Course::find($id);
        return View('courses.show', compact('course'));
    }

    public function showpdf($id){
        $course = Course::find($id);
        if(Storage::exists($course->pdf_path)){
            $filePath = storage_path('app/' . $course->pdf_path);

            // Get the content type of the PDF
            $contentType = mime_content_type($filePath);

            // Return the PDF file as a response
            $response = response()->file($filePath, ['Content-Type' => $contentType]);

            return $response;
        }else{
            return __('No pdf file');
        }        
    }

    public function update(Request $request, $id){
        $course = Course::find($id);
        
        $validatedData = $request->validate([
            'name' => 'required',
            'url' => 'url',
            //'pdf_path'  => 'required',
        ]);
        $course->update($validatedData);

        return redirect()->route(\App\Models\SettingLocal::getLang().'.admin.courses.index')->with('success', __('Course updated by succesfully'));
    }

    public function destroy($id){
        $course = Course::find($id);
        if(Storage::exists($course->pdf_path)){
            Storage::delete($course->pdf_path);
        }
        $course->delete();
        return redirect()->route(\App\Models\SettingLocal::getLang().'.admin.courses.index')->with('success', __('Course deleted by succesfully'));
    }


}
