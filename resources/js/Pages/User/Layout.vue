<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { formatNumber } from "@/helpers/numbers";

defineProps({
    user: Object,
    points: Number,
});
</script>

<template>
    <div class="mx-auto max-w-screen-2xl">
        <div class="mb-6">
            <div
                class="flex flex-col items-center justify-center sm:space-x-4 sm:flex-row"
            >
                <div class="avatar">
                    <div class="w-28 h-28 mask mask-squircle">
                        <img :src="user.data.profile_image_url" />
                    </div>
                </div>

                <div
                    class="flex flex-col items-center space-y-2 sm:items-start"
                >
                    <h1
                        class="text-4xl font-bold leading-tight tracking-wider truncate md:text-5xl"
                    >
                        {{ user.data.username }}
                    </h1>

                    <div class="space-x-3 text-lg badge badge-lg">
                        {{ formatNumber(points) }}
                        Bin Points
                    </div>
                </div>
            </div>
        </div>

        <div class="justify-center mb-6 uppercase tabs">
            <Link
                :href="route('user.bins', user.data.username)"
                class="tab tab-lg tab-bordered"
                :class="{
                    'tab-active': route().current('user.bins'),
                }"
            >
                Bins
            </Link>
            <Link
                :href="route('user.favorites', user.data.username)"
                class="tab tab-lg tab-bordered"
                :class="{
                    'tab-active': route().current('user.favorites'),
                }"
            >
                Favorites
            </Link>
        </div>

        <div>
            <slot />
        </div>
    </div>
</template>
