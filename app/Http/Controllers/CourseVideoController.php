<?php
namespace App\Http\Controllers;

use App\Models\CourseVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseVideoController extends Controller
{
    public function index()
    {
        $videos = CourseVideo::paginate(10);

        return response()->json(['data' => $videos], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_name' => 'required|string|max:255',
            'module_id' => 'required|exists:course_modules,id',
            'video_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $video = CourseVideo::create($request->all());

        return response()->json(['data' => $video], 201);
    }

    public function show($id)
    {
        $video = CourseVideo::find($id);

        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        return response()->json(['data' => $video], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'video_name' => 'string|max:255',
            'module_id' => 'exists:course_modules,id',
            'video_url' => 'url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $video = CourseVideo::find($id);

        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        $video->update($request->all());

        return response()->json(['data' => $video], 200);
    }

    public function destroy($id)
    {
        $video = CourseVideo::find($id);

        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        $video->delete();

        return response()->json(['message' => 'Video deleted successfully'], 200);
    }
}
