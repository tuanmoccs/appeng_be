<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListeningQuestion;
use App\Models\ListeningSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ListeningQuestionController extends Controller
{
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, $section)
  {
    $validator = Validator::make($request->all(), [
      'question' => 'required|string',
      'options' => 'required|array|min:2',
      'options.*' => 'required|string',
      'correct_answer' => 'required|string',
      'audio_file' => 'nullable|file|mimes:mp3,wav,m4a,ogg|max:10240',
      'audio_start_time' => 'nullable|integer|min:0',
      'audio_end_time' => 'nullable|integer|min:0',
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

      $questionData = [
        'listening_section_id' => $section,
        'question' => $request->question,
        'options' => $request->options,
        'correct_answer' => $request->correct_answer,
        'audio_start_time' => $request->audio_start_time,
        'audio_end_time' => $request->audio_end_time,
        'order' => $request->order,
      ];

      // Handle audio file for single type questions
      if ($request->hasFile('audio_file') && $sectionModel->question_type === 'single') {
        $audioFile = $request->file('audio_file');
        $audioPath = $audioFile->store('listening/questions', 'public');
        $questionData['audio_file'] = $audioPath;
      }

      $question = ListeningQuestion::create($questionData);

      // Update total questions count
      $sectionModel->listeningTest->updateTotalQuestions();

      return redirect()->route('admin.listening-tests.show', $sectionModel->listening_test_id)
        ->with('success', 'Question created successfully!');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Error creating question: ' . $e->getMessage())
        ->withInput();
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $section, $question)
  {
    $validator = Validator::make($request->all(), [
      'question' => 'required|string',
      'options' => 'required|array|min:2',
      'options.*' => 'required|string',
      'correct_answer' => 'required|string',
      'audio_file' => 'nullable|file|mimes:mp3,wav,m4a,ogg|max:10240',
      'audio_start_time' => 'nullable|integer|min:0',
      'audio_end_time' => 'nullable|integer|min:0',
      'order' => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput()
        ->with('error', 'Please check the form for errors.');
    }

    try {
      $questionModel = ListeningQuestion::findOrFail($question);
      $sectionModel = ListeningSection::findOrFail($section);

      $questionData = [
        'question' => $request->question,
        'options' => $request->options,
        'correct_answer' => $request->correct_answer,
        'audio_start_time' => $request->audio_start_time,
        'audio_end_time' => $request->audio_end_time,
        'order' => $request->order,
      ];

      // Handle audio file for single type questions
      if ($request->hasFile('audio_file') && $sectionModel->question_type === 'single') {
        $audioFile = $request->file('audio_file');
        $audioPath = $audioFile->store('listening/questions', 'public');
        $questionData['audio_file'] = $audioPath;

        // Delete old audio file if exists
        if ($questionModel->audio_file && Storage::disk('public')->exists($questionModel->audio_file)) {
          Storage::disk('public')->delete($questionModel->audio_file);
        }
      }

      $questionModel->update($questionData);

      return redirect()->route('admin.listening-tests.show', $sectionModel->listening_test_id)
        ->with('success', 'Question updated successfully!');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Error updating question: ' . $e->getMessage())
        ->withInput();
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($section, $question)
  {
    try {
      $questionModel = ListeningQuestion::findOrFail($question);
      $sectionModel = ListeningSection::findOrFail($section);

      // Delete audio file if exists
      if ($questionModel->audio_file && Storage::disk('public')->exists($questionModel->audio_file)) {
        Storage::disk('public')->delete($questionModel->audio_file);
      }

      $questionModel->delete();

      // Update total questions count
      $sectionModel->listeningTest->updateTotalQuestions();

      return redirect()->route('admin.listening-tests.show', $sectionModel->listening_test_id)
        ->with('success', 'Question deleted successfully!');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Error deleting question: ' . $e->getMessage());
    }
  }
}
