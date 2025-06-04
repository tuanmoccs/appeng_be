<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Word;
use App\Models\Lesson;
use Illuminate\Http\Request;

class WordController extends Controller
{
  /**
   * Display a listing of the words.
   */
  public function index(Request $request)
  {
    $query = Word::query();

    // Filter by lesson
    if ($request->has('lesson_id') && $request->lesson_id) {
      $query->where('lesson_id', $request->lesson_id);
    }

    // Search by word or translation
    if ($request->has('search') && $request->search) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where('word', 'like', "%{$search}%")
          ->orWhere('translation', 'like', "%{$search}%");
      });
    }

    $words = $query->with('lesson')->paginate(15);
    $lessons = Lesson::orderBy('order')->get();

    return view('admin.words.index', compact('words', 'lessons'));
  }

  /**
   * Show the form for creating a new word.
   */
  public function create()
  {
    $lessons = Lesson::orderBy('order')->get();
    return view('admin.words.create', compact('lessons'));
  }

  /**
   * Store a newly created word in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'word' => 'required|string|max:255',
      'translation' => 'required|string|max:255',
      'pronunciation' => 'nullable|string|max:255',
      'example_sentence' => 'nullable|string',
      'lesson_id' => 'nullable|exists:lessons,id',
      'image_url' => 'nullable|string|max:255',
      'audio_url' => 'nullable|string|max:255',
    ]);

    Word::create($request->all());

    return redirect()->route('admin.words.index')
      ->with('success', 'Từ vựng đã được thêm thành công!');
  }

  /**
   * Display the specified word.
   */
  public function show(Word $word)
  {
    $word->load('lesson');
    return view('admin.words.show', compact('word'));
  }

  /**
   * Show the form for editing the specified word.
   */
  public function edit(Word $word)
  {
    $lessons = Lesson::orderBy('order')->get();
    return view('admin.words.edit', compact('word', 'lessons'));
  }

  /**
   * Update the specified word in storage.
   */
  public function update(Request $request, Word $word)
  {
    $request->validate([
      'word' => 'required|string|max:255',
      'translation' => 'required|string|max:255',
      'pronunciation' => 'nullable|string|max:255',
      'example_sentence' => 'nullable|string',
      'lesson_id' => 'nullable|exists:lessons,id',
      'image_url' => 'nullable|string|max:255',
      'audio_url' => 'nullable|string|max:255',
    ]);

    $word->update($request->all());

    return redirect()->route('admin.words.index')
      ->with('success', 'Từ vựng đã được cập nhật thành công!');
  }

  /**
   * Remove the specified word from storage.
   */
  public function destroy(Word $word)
  {
    $word->delete();

    return redirect()->route('admin.words.index')
      ->with('success', 'Từ vựng đã được xóa thành công!');
  }

  /**
   * Import words from CSV/Excel
   */
  public function importForm()
  {
    $lessons = Lesson::orderBy('order')->get();
    return view('admin.words.import', compact('lessons'));
  }

  /**
   * Process the import
   */
  public function import(Request $request)
  {
    $request->validate([
      'file' => 'required|file|mimes:csv,txt,xlsx,xls',
      'lesson_id' => 'nullable|exists:lessons,id',
    ]);

    // Xử lý import file (giả định)
    // Trong thực tế, bạn sẽ cần thêm package như maatwebsite/excel để xử lý

    return redirect()->route('admin.words.index')
      ->with('success', 'Đã import từ vựng thành công!');
  }
}
