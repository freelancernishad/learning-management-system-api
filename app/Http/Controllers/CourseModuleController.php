<?php
namespace App\Http\Controllers;

use App\Models\CourseModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseModuleController extends Controller
{
    public function index()
    {
        $modules = CourseModule::with('videos')->paginate(10);

        return response()->json(['data' => $modules], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $module = CourseModule::create($request->all());

        return response()->json(['data' => $module], 201);
    }

    public function show($id)
    {
        $module = CourseModule::find($id);

        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        return response()->json(['data' => $module], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'module_name' => 'string|max:255',
            'course_id' => 'exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $module = CourseModule::find($id);

        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        $module->update($request->all());

        return response()->json(['data' => $module], 200);
    }

    public function destroy($id)
    {
        $module = CourseModule::find($id);

        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        $module->delete();

        return response()->json(['message' => 'Module deleted successfully'], 200);
    }
}
