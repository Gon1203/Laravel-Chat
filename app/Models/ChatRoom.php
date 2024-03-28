<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    use HasFactory;

    protected $table = 'chat_rooms';

    protected $guarded = [];

    protected $withCount = ['users'];

    protected $appends = ['is_joined_chat_room'];

    public function users( ): BelongsToMany
    {
        return $this->belongsToMany(User::class,'chat_room_user','chat_room_id','user_id');
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class,'chat_room_id','id');
    }

    public function isJoinedChatRoom(): Attribute
    {

        return new Attribute(
            get:function(){
                if (auth()->check()){
                    $user = auth()->user();
                    return $this->users()->where('user_id',$user->getKey())->exists();
                } else {
                    return false;
                }
            }
        );
    }
}
