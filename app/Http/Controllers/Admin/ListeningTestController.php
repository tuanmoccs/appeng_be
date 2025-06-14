<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListeningTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ListeningTestController extends Controller
{
  public function index()
  {
    $tests = ListeningTest::with('sections.questions')
      ->orderBy('created_at', 'desc')
      ->paginate(10);

    return view('admin.listening-tests.index', compact('tests'));
  }

  public function create()
  {
    return view('admin.listening-tests.create');
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'type' => 'required|in:ielts,toeic,toefl,general',
      'time_limit' => 'required|integer|min:1',
      'passing_score' => 'required|integer|min:0|max:100',
      'is_active' => 'boolean',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    }

    try {
      ListeningTest::create([
        'title' => $request->title,
        'description' => $request->description,
        'type' => $request->type,
        'time_limit' => $request->time_limit,
        'passing_score' => $request->passing_score,
        'is_active' => $request->has('is_active'),
        'total_questions' => 0,
      ]);

      return redirect()->route('admin.listening-tests.index')
        ->with('success', 'Listening test created successfully!');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Error creating test: ' . $e->getMessage())
        ->withInput();
    }
  }

  public function show($id)
  {
    $test = ListeningTest::with(['sections.questions' => function ($query) {
      $query->orderBy('order');
    }])->findOrFail($id);

    return view('admin.listening-tests.show', compact('test'));
  }

  public function edit($id)
  {
    $test = ListeningTest::findOrFail($id);
    return view('admin.listening-tests.edit', compact('test'));
  }

  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'type' => 'required|in:ielts,toeic,toefl,general',
      'time_limit' => 'required|integer|min:1',
      'passing_score' => 'required|integer|min:0|max:100',
      'is_active' => 'boolean',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    }

    try {
      $test = ListeningTest::findOrFail($id);
      $test->update([
        'title' => $request->title,
        'description' => $request->description,
        'type' => $request->type,
        'time_limit' => $request->time_limit,
        'passing_score' => $request->passing_score,
        'is_active' => $request->has('is_active'),
      ]);

      return redirect()->route('admin.listening-tests.show', $id)
        ->with('success', 'Test updated successfully!');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Error updating test: ' . $e->getMessage())
        ->withInput();
    }
  }

  public function destroy($id)
  {
    try {
      $test = ListeningTest::with('sections.questions')->findOrFail($id);

      // Delete all associated files
      foreach ($test->sections as $section) {
        if ($section->audio_file && Storage::disk('public')->exists($section->audio_file)) {
          Storage::disk('public')->delete($section->audio_file);
        }

        foreach ($section->questions as $question) {
          if ($question->audio_file && Storage::disk('public')->exists($question->audio_file)) {
            Storage::disk('public')->delete($question->audio_file);
          }
        }
      }

      $test->delete();

      return redirect()->route('admin.listening-tests.index')
        ->with('success', 'Test deleted successfully!');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Error deleting test: ' . $e->getMessage());
    }
  }

  public function toggleStatus($id)
  {
    try {
      $test = ListeningTest::findOrFail($id);
      $test->update(['is_active' => !$test->is_active]);

      return response()->json([
        'success' => true,
        'message' => 'Status updated successfully',
        'is_active' => $test->is_active
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Error updating status: ' . $e->getMessage()
      ], 500);
    }
  }
}
