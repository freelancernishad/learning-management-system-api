<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class StudentController extends Controller
{
    public function index(Request $request)
    {

        $perpage = $request->perpage;
        if($perpage){
            $students = Student::paginate($perpage);
        }else{
            $students = Student::all();
        }
        return response()->json($students);
    }

    public function store(Request $request)
    {

        Log::info(json_encode($request->all()));


        $validator = Validator::make($request->all(), [
            'founder_name' => 'required|string',
            'company_name' => 'required|string',
            'founder_email' => 'required|email',
            'location' => 'required|string',
            'founder_phone' => 'required|string',
            'business_category' => 'required|string',
            'founder_gender' => 'required|string',
            'website_url' => 'nullable|string',
            'employee_number' => 'nullable|integer',
            'formation_of_company' => 'nullable',
            'company_video_link' => 'nullable|string',
            'facebook_link' => 'nullable|string',
            'youtube_link' => 'nullable|string',
            'linkedin_link' => 'nullable|string',
            // 'attachment_file' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $validatedData = [
            'founder_name' => $request->founder_name,
            'company_name' => $request->company_name,
            'founder_email' => $request->founder_email,
            'location' => $request->location,
            'founder_phone' => $request->founder_phone,
            'business_category' => $request->business_category,
            'founder_gender' => $request->founder_gender,
            'website_url' => $request->website_url,
            'employee_number' => $request->employee_number,
            'formation_of_company' => $request->formation_of_company,
            'company_video_link' => $request->company_video_link,
            'facebook_link' => $request->facebook_link,
            'youtube_link' => $request->youtube_link,
            'linkedin_link' => $request->linkedin_link,
            'attachment_file' => $request->attachment_file,
        ];




        $student = Student::create($validatedData);
        return response()->json($student, 201);
    }

    public function show($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
        return response()->json($student);
    }

    public function update(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }


        $validator = Validator::make($request->all(), [
            'founder_name' => 'required|string',
            'company_name' => 'required|string',
            'founder_email' => 'required|email',
            'location' => 'required|string',
            'founder_phone' => 'required|string',
            'business_category' => 'required|string',
            'founder_gender' => 'required|string',
            'website_url' => 'nullable|string',
            'employee_number' => 'nullable|integer',
            'formation_of_company' => 'nullable',
            'company_video_link' => 'nullable|string',
            'facebook_link' => 'nullable|string',
            'youtube_link' => 'nullable|string',
            'linkedin_link' => 'nullable|string',
            'attachment_file' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $validatedData = [
            'founder_name' => $request->founder_name,
            'company_name' => $request->company_name,
            'founder_email' => $request->founder_email,
            'location' => $request->location,
            'founder_phone' => $request->founder_phone,
            'business_category' => $request->business_category,
            'founder_gender' => $request->founder_gender,
            'website_url' => $request->website_url,
            'employee_number' => $request->employee_number,
            'formation_of_company' => $request->formation_of_company,
            'company_video_link' => $request->company_video_link,
            'facebook_link' => $request->facebook_link,
            'youtube_link' => $request->youtube_link,
            'linkedin_link' => $request->linkedin_link,
            'attachment_file' => $request->attachment_file,
        ];

        $student->update($validatedData);
        return response()->json($student);
    }

    public function destroy($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully']);
    }
}



