<template>
    {{ data }}
    <input type="text" v-model="messageForm.message">
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    data: Object
})

const messageForm = useForm({
    messageType: 'T',
    message: null,
    images: FileList
})



</script>

<style scoped>

</style>