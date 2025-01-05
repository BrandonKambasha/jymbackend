<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ParticipantController extends Controller
{
    public function index()
    {
        $participants = Participant::all()->map(function ($participant) {
            $participant->image = $participant->image 
                ? URL::to('/images/' . $participant->image)
                : null;
            return $participant;
        });
        return response()->json($participants);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'weight' => 'nullable|numeric',
            'waist' => 'nullable|numeric',
            'hip' => 'nullable|numeric',
            'chest' => 'nullable|numeric',
            'pushups' => 'nullable|integer',
            'pullups' => 'nullable|integer',
            'weights_lifted' => 'nullable|numeric',
            'sprint_time' => 'nullable|numeric',
            'before_photo' => 'nullable|string',
            'after_photo' => 'nullable|string',
        ]);

        $validatedData['user_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $imageName = $validatedData['user_id'] . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validatedData['image'] = $imageName;
        }

        $participant = Participant::create($validatedData);
        $participant->image = $participant->image 
        ? URL::to('/images/' . $participant->image)
        : null;

        return response()->json($participant, 201);
    }

    public function show(Participant $participant)
    {
        $participant->image = $participant->image 
        ? URL::to('/images/' . $participant->image)
        : null;
    return response()->json($participant);
    }

    public function update(Request $request, Participant $participant)
    {
        // Log the original data before update
        \Log::info('Request Data:', $request->all());

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'weight' => 'nullable|numeric',
            'waist' => 'nullable|numeric',
            'hip' => 'nullable|numeric',
            'chest' => 'nullable|numeric',
            'pushups' => 'nullable|integer',
            'pullups' => 'nullable|integer',
            'weights_lifted' => 'nullable|numeric',
            'sprint_time' => 'nullable|numeric',
            'before_photo' => 'nullable|string',
            'after_photo' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $imageName = $participant->user_id . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validatedData['image'] = $imageName;
        }

        // Explicitly set null values
        foreach (['weight', 'waist', 'hip', 'chest', 'pushups', 'pullups', 'weights_lifted', 'sprint_time'] as $field) {
            if ($request->has($field) && $request->input($field) === '') {
                $validatedData[$field] = null;
            }
        }


        $participant->update($validatedData);

        // Log the updated data
        $participant->image = $participant->image 
        ? URL::to('/images/' . $participant->image)
        : null;
        return response()->json($participant);
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();

        return response()->json(null, 204);
    }
}

