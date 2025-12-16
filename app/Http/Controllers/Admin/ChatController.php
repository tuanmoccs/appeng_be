<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
  // Show chat page
  public function index()
  {
    $admin = Auth::guard('admin')->user();

    $conversations = Conversation::with(['user', 'lastMessage'])
      ->where('admin_id', $admin->id)
      ->orWhereNull('admin_id')
      ->orderBy('last_message_at', 'desc')
      ->get();

    return view('admin.chat.index', compact('conversations'));
  }

  // Get conversations via API
  public function getConversations()
  {
    $admin = Auth::guard('admin')->user();

    $conversations = Conversation::with(['user', 'lastMessage', 'admin'])
      ->where(function ($query) use ($admin) {
        $query->where('admin_id', $admin->id)
          ->orWhereNull('admin_id');
      })
      ->orderBy('last_message_at', 'desc')
      ->get()
      ->map(function ($conversation) {
        return [
          'id' => $conversation->id,
          'user' => [
            'id' => $conversation->user->id,
            'name' => $conversation->user->name,
            'avatar' => $conversation->user->avatar_url,
            'is_online' => $conversation->user->is_online,
          ],
          'admin' => $conversation->admin ? [
            'id' => $conversation->admin->id,
            'name' => $conversation->admin->name,
          ] : null,
          'last_message' => $conversation->lastMessage ? [
            'message' => $conversation->lastMessage->message,
            'created_at' => $conversation->lastMessage->created_at,
            'is_mine' => $conversation->lastMessage->sender_type === 'App\\Models\\Admin',
          ] : null,
          'unread_count' => $conversation->unreadMessagesCount(true),
          'last_message_at' => $conversation->last_message_at,
        ];
      });

    return response()->json($conversations);
  }

  // Assign conversation to admin
  public function assignConversation(Request $request, $conversationId)
  {
    $admin = Auth::guard('admin')->user();

    $conversation = Conversation::findOrFail($conversationId);

    if ($conversation->admin_id === null || $conversation->admin_id === $admin->id) {
      $conversation->update(['admin_id' => $admin->id]);
      return response()->json(['success' => true]);
    }

    return response()->json(['error' => 'Conversation already assigned'], 400);
  }

  // Get messages
  public function getMessages($conversationId)
  {
    $admin = Auth::guard('admin')->user();

    $conversation = Conversation::where('id', $conversationId)
      ->where(function ($query) use ($admin) {
        $query->where('admin_id', $admin->id)
          ->orWhereNull('admin_id');
      })
      ->firstOrFail();

    // Assign if not assigned
    if ($conversation->admin_id === null) {
      $conversation->update(['admin_id' => $admin->id]);
    }

    // Mark messages as read
    Message::where('conversation_id', $conversationId)
      ->where('sender_type', User::class)
      ->where('is_read', false)
      ->update([
        'is_read' => true,
        'read_at' => now(),
      ]);

    $messages = Message::where('conversation_id', $conversationId)
      ->with('sender')
      ->orderBy('created_at', 'asc')
      ->get()
      ->map(function ($message) use ($admin) {
        return [
          'id' => $message->id,
          'message' => $message->message,
          'type' => $message->type,
          'file_url' => $message->file_url,
          'is_mine' => $message->sender_id === $admin->id && $message->sender_type === 'App\\Models\\Admin',
          'sender' => [
            'name' => $message->sender_name,
            'avatar' => $message->sender_avatar,
          ],
          'is_read' => $message->is_read,
          'created_at' => $message->created_at,
        ];
      });

    return response()->json($messages);
  }

  // Send message
  public function sendMessage(Request $request, $conversationId)
  {
    $request->validate([
      'message' => 'required|string|max:5000',
    ]);

    $admin = Auth::guard('admin')->user();

    $conversation = Conversation::where('id', $conversationId)
      ->where('admin_id', $admin->id)
      ->firstOrFail();

    $message = Message::create([
      'conversation_id' => $conversationId,
      'sender_type' => Admin::class,
      'sender_id' => $admin->id,
      'message' => $request->message,
      'type' => 'text',
    ]);

    $conversation->update([
      'last_message_at' => now(),
    ]);

    return response()->json([
      'id' => $message->id,
      'message' => $message->message,
      'type' => $message->type,
      'is_mine' => true,
      'sender' => [
        'name' => $admin->name,
        'avatar' => $admin->avatar_url,
      ],
      'created_at' => $message->created_at,
    ]);
  }
}
