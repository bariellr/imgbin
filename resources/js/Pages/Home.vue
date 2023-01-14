<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { ref, computed, toRefs } from "vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import BinsGrid from "@/Components/BinsGrid.vue";
import EmptyState from "@/Components/EmptyState.vue";
import { PlusIcon } from "@heroicons/vue/solid";
import { useMediaQuery } from "@vueuse/core";

const props = defineProps({
    bins: Object,
    tags: Object,
    q: String,
});

const { bins, tags, q } = toRefs(props);

const isXl = useMediaQuery("(min-width: 1280px)");
const isLg = useMediaQuery("(min-width: 1024px)");
const isMd = useMediaQuery("(min-width: 768px)");

const shownTags = computed(() => {
    if (isShowingMoreTags.value) {
        return tags.value.data;
    }

    if (isXl.value) {
        return tags.value.data.slice(0, 10);
    }
    if (isLg.value) {
        return tags.value.data.slice(0, 8);
    }
    if (isMd.value) {
        return tags.value.data.slice(0, 6);
    }

    return tags.value.data.slice(0, 3);
});

const isShowingMoreTags = ref(false);

const toggleMoreTags = () => {
    isShowingMoreTags.value = !isShowingMoreTags.value;
};

const hasBins = computed(() => bins.value.data.length > 0);
</script>

<template>
    <Head title="Home" />

    <div class="mx-auto max-w-screen-2xl">
        <!-- <h1 class="mb-6 text-4xl font-bold leading-tight tracking-wider">
            Bins
        </h1> -->

        <!-- tags -->
        <div v-if="shownTags" class="mb-12 space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="font-bold tracking-wide uppercase">Explore Tags</h4>

                <button
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
                </button>
            </div>

            <div
                class="grid grid-cols-3 gap-3 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10"
            >
                <div
                    v-for="tag in shownTags"
                    class="shadow-lg card card-compact to-accent from-accent-focus bg-gradient-to-tr text-accent-content"
                >
                    <div class="card-body">
                        <h2 class="card-title">
                            <Link
                                class="truncate"
                                :href="route('tags.show', tag.slug)"
                            >
                                {{ tag.name }}
                                <span class="absolute inset-0"></span>
                            </Link>
                        </h2>
                        <p>{{ `${tag.bins_count} bins` }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- bins -->
        <div class="mb-12 space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="font-bold tracking-wide uppercase">
                    {{ q ? `Latest "${q}" Bins` : `Latest Bins` }}
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

            <BinsGrid v-if="hasBins" :bins="bins" />
            <EmptyState v-else class="my-16" />
        </div>
    </div>
</template>
