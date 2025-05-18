<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class WordController extends Controller{
    public function index()
    {
        $words = Word::all();
        return response()->json($words);
    }

    public function show($id)
    {
        $word = Word::findOrFail($id);
        return response()->json($word);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'word' => 'required|string|max:255',
            'translation' => 'required|string|max:255',
            'pronunciation' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:255',
            'audio_url' => 'nullable|string|max:255',
            'example_sentence' => 'nullable|string',
            'lesson_id' => 'nullable|exists:lessons,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $word = Word::create($request->all());
        return response()->json($word, 201);
    }

    public function update(Request $request, $id)
    {
        $word = Word::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'word' => 'sometimes|required|string|max:255',
            'translation' => 'sometimes|required|string|max:255',
            'pronunciation' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:255',
            'audio_url' => 'nullable|string|max:255',
            'example_sentence' => 'nullable|string',
            'lesson_id' => 'nullable|exists:lessons,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $word->update($request->all());
        return response()->json($word);
    }

    public function destroy($id)
    {
        $word = Word::findOrFail($id);
        $word->delete();
        return response()->json(null, 204);
    }
}
