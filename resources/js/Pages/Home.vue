<template>
    <div class="flex gap-x-4 m-4">
        <div class="flex flex-col border-2 p-4 rounded-lg bg-gray-200 gap-y-4">
            <input type="text" v-model="chatRoomForm.title" class="rounded-lg focus:ring-rose-500 focus:border-rose-700">
            <div class="w-full border-2 text-sm px-4 py-1 bg-white rounded-lg hover:cursor-pointer text-center" @click="onClickMakeChatRoom">
                Make Chat Room
            </div>
        </div>
        <div class="w-full">
            <div v-for="item in chatRoomList">
                <div class="bg-gray-700 px-10 py-2 border-2 border-white rounded-lg shadow-lg text-white flex justify-between items-center">
                    <div>
                        {{ item.title }} ({{ item.users_count }})
                    </div>
                        <button @click="onClickEnter({id: item.id})">
                            <template v-if="item.is_joined_chat_room">
                                ENTER
                            </template>
                            <template v-else>
                                JOIN
                            </template>
                        </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

import { router, useForm } from "@inertiajs/vue3";
import { inject } from "vue";

const props = defineProps({
    data: null,
    chatRoomList: null
})

const route = inject('route')

const form = useForm({
    sender: null,
    message: null
})

const chatRoomForm = useForm({
    title:null
})

function onClickSend( ){
    form.post(route('message.send'));
}

function onClickMakeChatRoom(){
    chatRoomForm.post(route('chat.room.store'))
}

function onClickEnter( { id }){
    router.visit(route('chat.room.show',id))
}
</script>

<style scoped>

</style>