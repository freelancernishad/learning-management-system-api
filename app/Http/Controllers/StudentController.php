<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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

    public function setRating(Request $request,$id)
    {

        $rating = $request->rating;
        $student = Student::find($id);
        $student->update(['rating'=>$rating]);
        return response()->json($student);
    }


    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'founder_name' => 'required|string',
        //     'company_name' => 'required|string',
        //     'founder_email' => 'required|email',
        //     'location' => 'required|string',
        //     'founder_phone' => 'required|string',
        //     'business_category' => 'required|string',
        //     'founder_gender' => 'required|string',
        //     'website_url' => 'nullable|string',
        //     'employee_number' => 'nullable|integer',
        //     'formation_of_company' => 'nullable',
        //     'company_video_link' => 'nullable|string',
        //     'facebook_link' => 'nullable|string',
        //     'youtube_link' => 'nullable|string',
        //     'linkedin_link' => 'nullable|string',
        //     // 'attachment_file' => 'nullable|string',
        // ]);
        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }
         $password = Hash::make($request->input('password'));



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
            'password' => $password,
            // 'attachment_file' => $request->attachment_file,
        ];

            // Check if there's a referral code in the request
            if ($request->has('ref_code')) {
                // Look up the student with the provided referral code
                $referer = Student::where('ref_code', $request->input('ref_code'))->first();
                if ($referer) {
                    // If found, set the referedby ID to the referer's ID
                    $validatedData['referedby'] = $referer->id;
                    
                    $referer->increment('refer_count');



                }
            }




  if ($request->hasFile('attachment_file')) {
        $file = $request->file('attachment_file');
         $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('attachments', $fileName, 'public');
        $validatedData['attachment_file'] = $filePath;

    }


    $validatedData['ref_code'] = $this->setRefCodeAttribute($request->founder_name);



    $student = new Student($validatedData);

        // $student = Student::create($validatedData);
        $student->save();
        return response()->json($student, 201);
    }



    public function setRefCodeAttribute($value)
    {
        $refCode = strtolower(str_replace(' ', '', $value)); // Remove spaces and convert to lowercase
        $counter = 1;
        // Check if the generated ref_code is unique, if not, append a counter until it becomes unique
        while (Student::where('ref_code', $refCode)->exists()) {
            $refCode = strtolower(str_replace(' ', '', $value)) . $counter;
            $counter++;
        }
        return $refCode;
    }

    public function show($id)
    {
        $student = Student::with('exams','referrals','enrollments')->find($id);
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

    function PaidStudents(){
        return $paidStudents = Student::getPaidStudents();
     }
}



