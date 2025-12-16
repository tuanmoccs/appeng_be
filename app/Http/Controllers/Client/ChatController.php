<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
  // Get list of available admins
  public function getAdmins()
  {
    $admins = Admin::select('id', 'name')
      // ->orderBy('is_online', 'desc')
      ->orderBy('name', 'asc')
      ->get()
      ->map(function ($admin) {
        return [
          'id' => $admin->id,
          'name' => $admin->name,
          // 'avatar' => $admin->avatar_url,
          // 'is_online' => $admin->is_online,
          // 'last_seen_at' => $admin->last_seen_at,
        ];
      });

    return response()->json($admins);
  }

  // Get user's conversations
  public function getConversations()
  {
    $user = Auth::user();

    $conversations = Conversation::with(['admin', 'lastMessage'])
      ->where('user_id', $user->id)
      ->orderBy('last_message_at', 'desc')
      ->get()
      ->map(function ($conversation) {
        return [
          'id' => $conversation->id,
          'admin' => [
            'id' => optional($conversation->admin)->id,
            'name' => optional($conversation->admin)->name ?? 'Unknown Admin',
            'is_online' => optional($conversation->admin)->is_online ?? false,
          ],
          'last_message' => $conversation->lastMessage ? [
            'message' => $conversation->lastMessage->message,
            'created_at' => $conversation->lastMessage->created_at,
            'is_mine' => $conversation->lastMessage->sender_type === 'App\\Models\\User',
          ] : null,
          'unread_count' => $conversation->unreadMessagesCount(false),
          'last_message_at' => $conversation->last_message_at,
        ];
      });

    return response()->json($conversations);
  }

  // Start or get conversation with admin
  public function startConversation(Request $request)
  {
    $request->validate([
      'admin_id' => 'required|exists:admins,id',
    ]);

    $user = Auth::user();

    $conversation = Conversation::firstOrCreate(
      [
        'user_id' => $user->id,
        'admin_id' => $request->admin_id,
      ],
      [
        'last_message_at' => now(),
      ]
    );

    return response()->json([
      'conversation_id' => $conversation->id,
      'admin' => [
        'id' => $conversation->admin->id,
        'name' => $conversation->admin->name,
        // 'avatar' => $conversation->admin->avatar_url,
        'is_online' => $conversation->admin->is_online,
      ],
    ]);
  }

  // Get messages in a conversation
  public function getMessages($conversationId)
  {
    $user = Auth::user();

    $conversation = Conversation::where('id', $conversationId)
      ->where('user_id', $user->id)
      ->firstOrFail();

    // Mark messages as read
    Message::where('conversation_id', $conversationId)
      ->where('sender_type', Admin::class)
      ->where('is_read', false)
      ->update([
        'is_read' => true,
        'read_at' => now(),
      ]);

    $messages = Message::where('conversation_id', $conversationId)
      ->with('sender')
      ->orderBy('created_at', 'asc')
      ->get()
      ->map(function ($message) use ($user) {
        return [
          'id' => $message->id,
          'message' => $message->message,
          'type' => $message->type,
          'file_url' => $message->file_url,
          'is_mine' => $message->sender_id === $user->id && $message->sender_type === 'App\\Models\\User',
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

  // Send a message
  public function sendMessage(Request $request, $conversationId)
  {
    $request->validate([
      'message' => 'required|string|max:5000',
    ]);

    $user = Auth::user();

    $conversation = Conversation::where('id', $conversationId)
      ->where('user_id', $user->id)
      ->firstOrFail();

    $message = Message::create([
      'conversation_id' => $conversationId,
      'sender_type' => User::class,
      'sender_id' => $user->id,
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
        'name' => $user->name,
        // 'avatar' => $user->avatar_url,
      ],
      'created_at' => $message->created_at,
    ]);
  }
}
