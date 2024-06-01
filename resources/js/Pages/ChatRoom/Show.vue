<template>
    <div class="mx-auto flex flex-col justify-center items-center w-full max-h-screen">
        <div id="chatContainer" class="w-full overflow-y-scroll">
            <template v-for="item in chatList">
                <template v-if="item?.user_id !== $page?.props?.auth?.user?.id">
                    <div class="w-full flex items-center justify-start">
                        <div class="max-w-[50%] border-2 rounded-lg px-3 py-2 bg-gray-100">
                            <div class="text-sm text-gray-500 flex justify-between items-center gap-x-4">
                                <div>
                                    {{ item?.user?.name }}
                                </div>
                                <div>
                                    {{ item?.created_at }}
                                </div>
                            </div>
                            <div class="break-words">
                                {{ item?.message }}
                            </div>
                            <template v-if="item?.media?.length > 0">
                                <div class="w-full h-full flex">
                                    <div v-for="image in item.media" class="max-w-[30%]">
                                        <img :src="image.original_url" alt="">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="w-full flex items-center justify-end">
                        <div class="max-w-[50%] border-2 rounded-lg px-3 py-2 bg-yellow-100">
                            <div class="text-sm text-gray-500 flex justify-between items-center gap-x-4">
                                <div>
                                    {{ item?.user?.name }}
                                </div>
                                <div>
                                    {{ item?.created_at }}
                                </div>
                            </div>
                            <div class="break-words">
                                {{ item?.message }}
                            </div>
                            <template v-if="item?.media?.length > 0">
                                <div class="w-full h-full flex">
                                    <div v-for="image in item.media" class="">
                                        <img :src="image.original_url" alt="">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </template>
        </div>
        <form class="flex" @submit.prevent="onClickSubmit">
            <div class="relative">
                <input v-model="messageForm.message" class="w-full" type="text">
                <span
                    class="absolute right-1.5 top-1.5 hover:cursor-pointer hover:font-semibold hover:border-gray-900 text-xl border-2 rounded-full px-2"
                    @click="onclickAddMedia"
                >
                    +
                </span>
            </div>
            <input id="media-input" accept="image/*" class="hidden" multiple type="file" @change="onChangeMediaInput">
            <button class="rounded-lg bg-gray-900 text-white border-2 px-4 py-2" @click="onClickSubmit"> SUBMIT</button>
        </form>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { inject, ref } from "vue";

const props = defineProps( {
    data: Object
} )

const route = inject( 'route' )

const messageForm = useForm( {
    messageType: 'T',
    message: null,
    images: FileList
} )

function onClickSubmit() {
    messageForm.post( route( 'chat.room.message.send', props?.data?.id ), {
        onSuccess: () => {
            console.log( 'success' )
            messageForm.reset()
        },
        onError: ( err ) => {
            console.log( err )
        },
        preserveState: true,
        preserveScroll: true
    } );
}

const chatList = ref( props?.data?.chats ?? [] );

// Get the scrollable div element

// Function to scroll to the bottom of the div
function scrollToBottom() {
    let scrollableDiv = document.getElementById( 'chatContainer' );
    scrollableDiv.scrollTop = scrollableDiv.scrollHeight;
}

Echo.channel( `chatRoom.${ props.data.id }` ).listen( '.message.sent', ( e ) => {
    chatList.value.push( e.chat );
    scrollToBottom()
} )

function onclickAddMedia() {
    document.getElementById( 'media-input' ).click()
}

function onChangeMediaInput( e ) {
    messageForm.images = e.target.files
}


</script>

<style scoped>

</style>