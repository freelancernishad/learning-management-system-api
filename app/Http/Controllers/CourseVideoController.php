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
            'course_module_id' => 'required|exists:course_modules,id',
            // 'video_url' => 'required|url',
            'videoFile' => 'required|file|mimes:mp4,mov,avi,wmv|max:204800', // Adjusted validation rule for video files
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

           // Handle file upload for video_url
        if ($request->hasFile('videoFile')) {
            $file = $request->file('videoFile');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('course_videos', $fileName, 'protected');
        } else {
            return response()->json(['error' => 'No video file provided.'], 422);
        }

        // Create the course video record
        $courseVideo = CourseVideo::create([
            'video_name' => $request->input('video_name'),
            'course_module_id' => $request->input('course_module_id'),
            'description' => $request->input('description'),
            'video_url' => url('course-video/'.$filePath),
        ]);

        return response()->json(['data' => $courseVideo], 201);
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
            'course_module_id' => 'exists:course_modules,id',
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
