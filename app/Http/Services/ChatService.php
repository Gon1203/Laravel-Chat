<?php

namespace App\Http\Services;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ChatService
{


    /**
     * @throws ValidationException
     */
    public function handleSendChat(array $data, $chatRoomId, $images = null): void
    {
        Validator::make($data, [
            'message' => ['required'],
            'messageType' => 'required',
        ])
            ->validate();
        /** @var  $chatRoom ChatRoom */
        $chatRoom = ChatRoom::query()
            ->findOrFail($chatRoomId);

        /** @var  $chat  Chat */
        $chat = $chatRoom->chats()
            ->create([
                'user_id' => auth()
                    ->user()
                    ->getKey(),
                'message' => $data['message']
            ]);

        if (!is_null($images)) {
            foreach ($images as $image) {
                try {
                    $chat->addMedia($image)
                        ->toMediaCollection('images');
                } catch (FileDoesNotExist $e) {
                } catch (FileIsTooBig $e) {
                }
            }
        }
        MessageSent::dispatch($chat);
    }

    public function handleChatRoomIndex(): Collection|array
    {
        return ChatRoom::query()
            ->get();
    }

    public function handleChatRoomShow($chatRoomId): Model|Collection|Builder|array|null
    {
        return ChatRoom::query()
            ->with([
                'users',
                'chats'
            ])
            ->findOrFail($chatRoomId);
    }

    /**
     * @throws ValidationException
     */
    public function handleChatRoomStore(array $data): void
    {
        Validator::make($data, [
            'title' => 'required'
        ])
            ->validate();

        /** @var $chatRoom ChatRoom */
        $chatRoom = ChatRoom::query()
            ->create(['title' => $data['title']]);

        $chatRoom->users()
            ->attach(auth()
                ->user()
                ->getKey());
    }

    public function handleCheckJoinedUser($chatRoomId): void
    {
        /** @var  $user User */
        $user = auth()->user();
        try {
            ChatRoom::query()
                ->whereHas('users', function ($sql) use ($user) {
                    $sql->where('user_id', $user->getKey());
                })
                ->findOrFail($chatRoomId);
        } catch (ModelNotFoundException) {

            ChatRoom::query()
                ->whereKey($chatRoomId)
                ->firstOrFail()
                ->users()
                ->attach($user->getKey());
        }

    }
}