<?php

namespace App\Http\Services;

use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ChatHelper
{

    const MESSAGE_TYPE_LIST = [
        'IMAGE' => 'I',
        'TEXT' => 'T'
    ];
    /**
     * @throws ValidationException
     */
    public function handleSendMessage(array $data){
        Validator::make($data,['sender'=>['required'],'message'=>['required']])->validate();

    }

    public function handleChatRoomIndex(): Collection|array
    {
        return ChatRoom::query()->get();
    }

    public function handleChatRoomShow($chatRoomId){
        return ChatRoom::query()->with(['users','chats'])->findOrFail($chatRoomId);
    }

    /**
     * @throws ValidationException
     */
    public function handleChatRoomStore(array $data): void
    {
        Validator::make($data,[
            'title' => 'required'
        ])->validate();

        /** @var $chatRoom ChatRoom*/
        $chatRoom = ChatRoom::query()->create(['title' => $data['title']]);

        $chatRoom->users()->attach(auth()->user()->getKey());
    }

    public function handleCheckJoinedUser($chatRoomId): void
    {
        /** @var  $user User */
        $user = auth()->user();
        try {
            ChatRoom::query()
                ->whereHas('users', function ($sql) use($user) {
                    $sql->where('user_id', $user->getKey());
                })
                ->findOrFail($chatRoomId);
        } catch (ModelNotFoundException){

            ChatRoom::query()
                ->whereKey($chatRoomId)
                ->firstOrFail()
                ->users()
                ->attach($user->getKey());
        }

    }
}