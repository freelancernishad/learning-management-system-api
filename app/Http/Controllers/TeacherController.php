<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all users with the role 'teacher'


        $perpage = $request->perpage;
        if($perpage){
            $teachers = User::where('role', 'teacher')->paginate($perpage);
        }else{
            $teachers = User::where('role', 'teacher')->get();
        }

        return response()->json($teachers);
    }

    public function show($id)
    {
        // Fetch a single user with the role 'teacher' by ID
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        return response()->json($teacher);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the teacher user with the role 'teacher'
        $teacher = new User();
        $teacher->name = $request->input('name');
        $teacher->email = $request->input('email');
        $teacher->password = Hash::make($request->password);
        $teacher->role = 'teacher';
        // Set other teacher fields if needed
        $teacher->save();

        return response()->json(['message' => 'Teacher created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data for updating the teacher


        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the teacher user with the role 'teacher'
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        $teacher->name = $request->input('name');
        $teacher->email = $request->input('email');
        // Update other teacher fields if needed
        $teacher->save();

        return response()->json(['message' => 'Teacher updated successfully'], 200);
    }

    public function destroy($id)
    {
        // Delete the teacher user with the role 'teacher'
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted successfully'], 200);
    }
}
