<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\EncryptionService;

class Message extends Model
{
  use HasFactory;

  protected $fillable = [
    'conversation_id',
    'sender_type',
    'sender_id',
    'message',
    'type',
    'file_url',
    'is_read',
    'read_at',
  ];

  protected $casts = [
    'is_read' => 'boolean',
    'read_at' => 'datetime',
  ];

  protected $appends = ['sender_name', 'sender_avatar'];

  public function setMessageAttribute($value)
  {
    $this->attributes['message'] = EncryptionService::encrypt($value);
  }

  public function getMessageAttribute($value)
  {
    return EncryptionService::decrypt($value);
  }

  public function conversation()
  {
    return $this->belongsTo(Conversation::class);
  }

  public function sender()
  {
    return $this->morphTo();
  }

  public function getSenderNameAttribute()
  {
    return $this->sender ? $this->sender->name : 'Unknown';
  }

  public function getSenderAvatarAttribute()
  {
    return $this->sender ? $this->sender->avatar_url : null;
  }
}
