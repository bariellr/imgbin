<script setup>
import { ref, watch } from "vue";
import { Link, usePage } from "@inertiajs/inertia-vue3";
import Modal from "@/Components/Modal.vue";

const authIsRequired = ref(false);

const closeModal = () => (authIsRequired.value = false);

watch(usePage().props, (props) => {
    authIsRequired.value = props.auth.is_required;
});
</script>

<template>
    <Modal :isOpen="authIsRequired" @closeModal="closeModal">
        <template #title> Authentication Required </template>

        <p>Please sign in to continue.</p>

        <template #actions>
            <Link :href="route('login')" class="normal-case btn btn-ghost"
                >Sign in</Link
            >
            <Link :href="route('register')" class="normal-case btn btn-primary"
                >Sign up</Link
            >
        </template>
    </Modal>
</template>
