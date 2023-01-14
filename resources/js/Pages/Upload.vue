<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { computed, onMounted } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { Head } from "@inertiajs/inertia-vue3";
import { useUploadModalStore } from "@/stores/uploadModal";

const uploadModal = useUploadModalStore();

onMounted(() => {
    uploadModal.open();
});

uploadModal.$onAction(({ name, store }) => {
    if (name === "close") {
        Inertia.get(route("home.index"));
    }
});
</script>

<template>
    <Head title="Upload" />
</template>
