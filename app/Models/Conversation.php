<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'admin_id',
    'last_message_at',
  ];

  protected $casts = [
    'last_message_at' => 'datetime',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }

  public function messages()
  {
    return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
  }

  public function lastMessage()
  {
    return $this->hasOne(Message::class)->latestOfMany();
  }

  public function unreadMessagesCount($forAdmin = false)
  {
    if ($forAdmin) {
      // Count unread messages from user
      return $this->messages()
        ->where('is_read', false)
        ->where('sender_type', User::class)
        ->count();
    } else {
      // Count unread messages from admin
      return $this->messages()
        ->where('is_read', false)
        ->where('sender_type', Admin::class)
        ->count();
    }
  }
}
