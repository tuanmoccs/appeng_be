<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLessonProgress;
use App\Models\UserQuizResult;
use App\Models\UserAchievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
  /**
   * Display a listing of the users.
   */
  public function index()
  {
    $users = User::withCount(['lessonProgress', 'quizResults'])
      ->latest()
      ->paginate(10);

    return view('admin.users.index', compact('users'));
  }

  /**
   * Show the form for creating a new user.
   */
  public function create()
  {
    return view('admin.users.create');
  }

  /**
   * Store a newly created user in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:6|confirmed',
      'avatar' => 'nullable|string',
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'avatar' => $request->avatar,
      'email_verified_at' => now(),
    ]);

    return redirect()->route('admin.users.index')
      ->with('success', 'Người dùng đã được tạo thành công!');
  }

  /**
   * Display the specified user.
   */
  public function show(User $user)
  {
    $user->load([
      'lessonProgress' => function ($query) {
        $query->where('is_completed', true)->with('lesson');
      },
      'quizResults' => function ($query) {
        $query->with('quiz');
      },
      'achievements',
      'stats'
    ]);

    return view('admin.users.show', compact('user'));
  }

  /**
   * Show the form for editing the specified user.
   */
  public function edit(User $user)
  {
    return view('admin.users.edit', compact('user'));
  }

  /**
   * Update the specified user in storage.
   */
  public function update(Request $request, User $user)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => [
        'required',
        'string',
        'email',
        'max:255',
        Rule::unique('users')->ignore($user->id),
      ],
      'password' => 'nullable|string|min:6|confirmed',
      'avatar' => 'nullable|string',
    ]);

    $userData = [
      'name' => $request->name,
      'email' => $request->email,
      'avatar' => $request->avatar,
    ];

    if ($request->filled('password')) {
      $userData['password'] = Hash::make($request->password);
    }

    $user->update($userData);

    return redirect()->route('admin.users.index')
      ->with('success', 'Thông tin người dùng đã được cập nhật thành công!');
  }

  /**
   * Remove the specified user from storage.
   */
  public function destroy(User $user)
  {
    $user->delete();

    return redirect()->route('admin.users.index')
      ->with('success', 'Người dùng đã được xóa thành công!');
  }
}
