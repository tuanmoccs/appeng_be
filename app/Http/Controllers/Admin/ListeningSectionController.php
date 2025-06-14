<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListeningSection;
use App\Models\ListeningTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ListeningSectionController extends Controller
{
  public function store(Request $request, $test)
  {
    $validator = Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'instructions' => 'nullable|string',
      'audio_file' => 'required|file|mimes:mp3,wav,m4a,ogg|max:20480', // 20MB max
      'question_type' => 'required|in:single,multiple',
      'order' => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput()
        ->with('error', 'Please check the form for errors.');
    }

    try {
      // Upload audio file
      $audioFile = $request->file('audio_file');
      $audioPath = $audioFile->store('listening/sections', 'public');

      // Get audio duration (basic implementation)
      $audioDuration = $this->getAudioDuration(storage_path('app/public/' . $audioPath));

      $section = ListeningSection::create([
        'listening_test_id' => $test, // Use the test parameter
        'title' => $request->title,
        'instructions' => $request->instructions,
        'audio_file' => $audioPath,
        'audio_duration' => $audioDuration,
        'order' => $request->order,
        'question_type' => $request->question_type,
      ]);

      return redirect()->route('admin.listening-tests.show', $test)->with('success', 'Section created successfully!');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Error creating section: ' . $e->getMessage())
        ->withInput();
    }
  }

  public function update(Request $request, $test, $section)
  {
    $validator = Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'instructions' => 'nullable|string',
      'audio_file' => 'nullable|file|mimes:mp3,wav,m4a,ogg|max:20480',
      'question_type' => 'required|in:single,multiple',
      'order' => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput()
        ->with('error', 'Please check the form for errors.');
    }

    try {
      $sectionModel = ListeningSection::findOrFail($section);

      $updateData = [
        'title' => $request->title,
        'instructions' => $request->instructions,
        'question_type' => $request->question_type,
        'order' => $request->order,
      ];

      // Handle audio file update
      if ($request->hasFile('audio_file')) {
        // Delete old audio file
        if ($sectionModel->audio_file && Storage::disk('public')->exists($sectionModel->audio_file)) {
          Storage::disk('public')->delete($sectionModel->audio_file);
        }

        // Upload new audio file
        $audioFile = $request->file('audio_file');
        $audioPath = $audioFile->store('listening/sections', 'public');
        $audioDuration = $this->getAudioDuration(storage_path('app/public/' . $audioPath));

        $updateData['audio_file'] = $audioPath;
        $updateData['audio_duration'] = $audioDuration;
      }

      $sectionModel->update($updateData);

      return redirect()->route('admin.listening-tests.show', $test)->with('success', 'Section updated successfully!');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Error updating section: ' . $e->getMessage())
        ->withInput();
    }
  }

  public function destroy($test, $section)
  {
    try {
      $sectionModel = ListeningSection::with('questions')->findOrFail($section);
      $testId = $sectionModel->listening_test_id;

      // Delete audio files
      if ($sectionModel->audio_file && Storage::disk('public')->exists($sectionModel->audio_file)) {
        Storage::disk('public')->delete($sectionModel->audio_file);
      }

      foreach ($sectionModel->questions as $question) {
        if ($question->audio_file && Storage::disk('public')->exists($question->audio_file)) {
          Storage::disk('public')->delete($question->audio_file);
        }
      }

      $sectionModel->delete();

      // Update total questions count
      $testModel = ListeningTest::find($testId);
      if ($testModel) {
        $testModel->updateTotalQuestions();
      }

      return redirect()->route('admin.listening-tests.show', $test)->with('success', 'Section deleted successfully!');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Error deleting section: ' . $e->getMessage());
    }
  }

  private function getAudioDuration($filePath)
  {
    try {
      // Try using getID3 if available
      if (class_exists('\getID3')) {
        $getID3 = new \getID3;
        $fileInfo = $getID3->analyze($filePath);
        if (isset($fileInfo['playtime_seconds'])) {
          return (int) $fileInfo['playtime_seconds'];
        }
      }

      // Try using ffprobe if available
      if (function_exists('shell_exec')) {
        $duration = shell_exec("ffprobe -i '$filePath' -show_entries format=duration -v quiet -of csv='p=0' 2>/dev/null");
        if ($duration && is_numeric(trim($duration))) {
          return (int) floatval(trim($duration));
        }
      }

      // Fallback - estimate based on file size (very rough)
      $fileSize = filesize($filePath);
      $estimatedDuration = $fileSize / 16000; // Rough estimate for MP3 at 128kbps
      return (int) $estimatedDuration;
    } catch (\Exception $e) {
      // Return 0 if can't determine duration
      return 0;
    }
  }
}
