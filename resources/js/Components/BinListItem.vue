<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import Image from "@/Components/Image.vue";

defineProps({
    bin: Object,
});
</script>

<template>
    <div class="shadow-xl card card-compact bg-base-300 text-base-content">
        <div class="card-body">
            <div class="flex space-x-4">
                <div class="avatar">
                    <div class="w-12 h-12 mask mask-squircle">
                        <Image
                            :image="bin.cover_image"
                            :enableLightbox="false"
                            dimensions="120x120"
                            :options="{ fit: 'crop' }"
                            class="object-cover w-full"
                        />
                    </div>
                </div>

                <Link :href="route('bin.show', bin.id)" class="flex-1">
                    <span>{{ bin.title }}</span>
                    <span class="absolute inset-0"></span>
                </Link>
            </div>
        </div>
    </div>
</template>
