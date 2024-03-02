<?php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type;
        if($type=='courselist'){
            $courses = Course::all();
        }else{
            $courses = Course::with(['category', 'modules.videos', 'students'])->paginate(10);
        }
        return response()->json(['data' => $courses], 200);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'course_name' => 'required|string',
            'course_category_id' => 'required|exists:course_categories,id',
            'instructor' => 'nullable|string',
            'rating' => 'nullable|numeric',
            // 'price' => 'required|numeric',
            'previousPrice' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'about_video' => 'nullable|string',
            'targeted_audience' => 'nullable|string',
            'descriptions' => 'nullable|string',
            'requirements' => 'nullable|string',
            'whatUlearn' => 'nullable|array',
            'whatUlearn.*' => 'string',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'demo_certificate' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        // Calculate discounted price
        $price = $request->input('previousPrice') - ($request->input('previousPrice') * ($request->input('discount') / 100));

        // Handle file upload for demo_certificate
        if ($request->hasFile('demo_certificate')) {
            $file = $request->file('demo_certificate');
            $path = $file->store('demo_certificates');
        } else {
            $path = null;
        }

        // Process array inputs
        $whatUlearn = $request->input('whatUlearn') ? json_encode($request->input('whatUlearn')) : null;
        $features = $request->input('features') ? json_encode($request->input('features')) : null;

        // Create the course
        $course = Course::create([
            'course_name' => $request->input('course_name'),
            'course_category_id' => $request->input('course_category_id'),
            'instructor' => $request->input('instructor'),
            'rating' => $request->input('rating'),
            'price' => $price,
            'previous_price' => $request->input('previousPrice'),
            'discount' => $request->input('discount'),
            'about_video' => $request->input('about_video'),
            'targeted_audience' => $request->input('targeted_audience'),
            'descriptions' => $request->input('descriptions'),
            'requirements' => $request->input('requirements'),
            'what_you_learn' => $whatUlearn,
            'features' => $features,
            'demo_certificate' => $path,
        ]);

        return response()->json(['data' => $course], 201);
    }

    public function show($id)
    {
        $course = Course::with(['category', 'modules.videos', 'students'])->find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        return response()->json(['data' => $course], 200);
    }
    public function getcourses($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        return response()->json(['data' => $course], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'course_name' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $course->update($request->all());

        return response()->json(['data' => $course], 200);
    }

    public function destroy($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully'], 200);
    }
}

