<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class BatchController extends Controller
{
    public function index(Request $request)
    {


        $perpage = $request->perpage;
        if($perpage){
            $batches = Batch::with('students')->paginate($perpage);
        }else{
            $batches = Batch::with('students')->get();
        }

        return response()->json($batches);
    }

    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:batches',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $batch = Batch::create($request->only('name'));

        return response()->json(['message' => 'Batch created successfully'], 201);
    }

    public function show($id)
    {
        $batch = Batch::with('students')->findOrFail($id);
        return response()->json($batch);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:batches,name,' . $id,
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $batch = Batch::findOrFail($id);
        $batch->update($request->only('name'));

        return response()->json(['message' => 'Batch updated successfully'], 200);
    }

    public function destroy($id)
    {
        $batch = Batch::findOrFail($id);
        $batch->delete();

        return response()->json(['message' => 'Batch deleted successfully'], 200);
    }
}
