<?php
namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function index()
    {
        // Fetch all questions with their answers (eager load the answers)
        $questions = Question::with('answers')->get();
        return response()->json($questions);
    }

    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'question_text' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Create the question
        $question = new Question();
        $question->question_text = $request->input('question_text');
        $question->save();

        // If answers are provided, store them for the question
        if ($request->has('answers') && is_array($request->input('answers'))) {
            foreach ($request->input('answers') as $answerText) {
                $answer = new Answer();
                $answer->answer_text = $answerText;
                $question->answers()->save($answer);
            }
        }

        return response()->json(['message' => 'Question created successfully'], 201);
    }

    public function show($id)
    {
        // Fetch a single question with its answers (eager load the answers)
        $question = Question::with('answers')->findOrFail($id);
        return response()->json($question);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data for question
        $request->validate([
            'question_text' => 'required|string',
        ]);

        // Update the question
        $question = Question::findOrFail($id);
        $question->question_text = $request->input('question_text');
        $question->save();

        // If answers are provided, update them for the question
        if ($request->has('answers') && is_array($request->input('answers'))) {
            $question->answers()->delete();
            foreach ($request->input('answers') as $answerText) {
                $answer = new Answer();
                $answer->answer_text = $answerText;
                $question->answers()->save($answer);
            }
        }

        return response()->json(['message' => 'Question updated successfully'], 200);
    }

    public function destroy($id)
    {
        // Delete the question and its associated answers
        $question = Question::findOrFail($id);
        $question->answers()->delete();
        $question->delete();

        return response()->json(['message' => 'Question deleted successfully'], 200);
    }
}
