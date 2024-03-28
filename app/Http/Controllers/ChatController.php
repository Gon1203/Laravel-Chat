<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Services\ChatHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends BaseController
{
    use ChatHelper;

    public function __construct()
    {
        Inertia::share(['messageTypeList' => self::MESSAGE_TYPE_LIST]);
    }

    public function home(){
        return Inertia::render('Home',[
            'chatRoomList' => $this->handleChatRoomIndex()
        ]);
    }

    public function sendMessage(Request $request){
        $data = $request->only(['message','sender']);
        try {
            $this->handleSendMessage($data);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
        return back();
    }

    public function chatRoomShow(Request $request, $chatRoomId): Response
    {
        $this->handleCheckJoinedUser($chatRoomId);
        return Inertia::render('ChatRoom/Show',[
            'data'=> function() use($chatRoomId){
                return $this->handleChatRoomShow($chatRoomId);
            }
        ]);
    }

    public function chatRoomStore(Request $request){
        $data = $request->only(['title']);
        try {
            $this->handleChatRoomStore($data);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
        return back();
    }



}