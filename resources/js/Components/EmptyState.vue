<script setup>
import { ArchiveIcon } from "@heroicons/vue/outline";

defineProps({
    message: {
        type: String,
        default: `There's nothing here!`,
    },
});
</script>

<template>
    <div class="flex items-center justify-center">
        <div
            class="flex flex-col items-center justify-center w-full max-w-lg px-10 py-20 space-y-4 text-center rounded-2xl text-base-content/70 bg-base-200"
        >
            <ArchiveIcon class="w-12 h-12 text-secondary" />

            <h2 class="text-2xl font-bold">
                {{ message }}
            </h2>
        </div>
    </div>
</template>
