<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends BaseController
{

    const MESSAGE_TYPE_LIST = [
        'IMAGE' => 'I',
        'TEXT' => 'T'
    ];
    private ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
        Inertia::share(['messageTypeList' => self::MESSAGE_TYPE_LIST]);
    }

    public function home()
    {
        return Inertia::render('Home', [
            'chatRoomList' => $this->chatService->handleChatRoomIndex()
        ]);
    }

    public function sendChat(Request $request, $roomId)
    {
        $data = $request->only([
            'message',
            'messageType'
        ]);
        $images = null;
        if ($request->hasFile('images')) {
            $images = $request->file('images');
        }
        try {
            $this->chatService->handleSendChat($data, $roomId, $images);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
        return back();
    }

    public function chatRoomShow(Request $request, $chatRoomId): Response
    {
        $this->chatService->handleCheckJoinedUser($chatRoomId);
        return Inertia::render('ChatRoom/Show', [
            'data' => function () use ($chatRoomId) {
                return $this->chatService->handleChatRoomShow($chatRoomId);
            }
        ]);
    }

    public function chatRoomStore(Request $request)
    {
        $data = $request->only(['title']);
        try {
            $this->chatService->handleChatRoomStore($data);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
        return back();
    }


}