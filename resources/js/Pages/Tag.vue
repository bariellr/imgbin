<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { Head } from "@inertiajs/inertia-vue3";
import BinsGrid from "@/Components/BinsGrid.vue";

defineProps({
    tag: Object,
    bins: Object,
});
</script>

<template>
    <Head :title="`${tag.data.name} Bins`" />

    <div class="mx-auto max-w-screen-2xl">
        <!-- hero -->
        <div class="mb-6 min-h-16 hero">
            <div class="text-center hero-content">
                <h1 class="text-5xl font-bold leading-tight tracking-wider">
                    {{ `${tag.data.name}` }}
                </h1>
                <!-- <p class="py-6 text-lg uppercase">
                        {{ `${tag.data.bins_count} Bins` }}
                    </p> -->
            </div>
        </div>

        <!-- bins -->
        <div class="mb-12 space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="font-bold tracking-wide uppercase">
                    Latest Bins in <span>{{ `${tag.data.name}` }}</span>
                </h4>

                <!-- <button
                    type="button"
                    @click="toggleMoreTags"
                    class="flex items-center space-x-2 font-bold tracking-wide uppercase btn-sm btn btn-ghost"
                >
                    <PlusIcon
                        class="w-4 h-4"
                        :class="{ 'rotate-45': isShowingMoreTags }"
                    />

                    <span>{{
                        !isShowingMoreTags ? `More tags` : `Less tags`
                    }}</span>
                </button> -->
            </div>

            <BinsGrid :bins="bins" />
        </div>
    </div>
</template>
