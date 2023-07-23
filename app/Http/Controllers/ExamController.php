<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $studentId = $request->studentId;
        if($studentId){
            $exams = Student::with('exams')->get();
        }else{

            $exams = Exam::with('student')->get();
        }
        return response()->json($exams);
    }

    public function store(Request $request)
    {
        $results = $request->results;
        $userId = $request->userId;

        // Create the exams and store the scores in the pivot table
        DB::transaction(function () use ($results, $userId) {
            $student = Student::findOrFail($userId);

            foreach ($results as $key => $value) {
                $exam = Exam::create([
                    'user_id' => $userId,
                    'name' => 'exam name',
                    'question' => $key,
                    'ans' => $value,
                ]);

                // Attach the exam to the student and store the score in the pivot table
                $student->exams()->attach($exam, ['score' => 1]);
            }
        });

        return response()->json(['message' => 'Exams created successfully'], 201);
    }
    public function show($id)
    {
        $exam = Exam::with('student')->findOrFail($id);
        return response()->json($exam);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:students,id',
            'name' => 'required|string|unique:exams,name,' . $id,
            'question' => 'required|string',
            'ans' => 'required|string',
        ]);

        $exam = Exam::findOrFail($id);
        $exam->update($request->all());

        return response()->json(['message' => 'Exam updated successfully'], 200);
    }

    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        return response()->json(['message' => 'Exam deleted successfully'], 200);
    }
}
