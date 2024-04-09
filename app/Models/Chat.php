<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Chat extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'chats';
    protected $guarded = [];

    protected $with = ['user'];

    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format("Y-m-d H:i:s");
    }
}
