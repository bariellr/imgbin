<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { computed, toRefs } from "vue";
import { Head } from "@inertiajs/inertia-vue3";
import Layout from "@/Pages/User/Layout.vue";
import BinsGrid from "@/Components/BinsGrid.vue";
import EmptyState from "@/Components/EmptyState.vue";

const props = defineProps({
    user: Object,
    bins: Object,
    points: Number,
});

const { user, bins, points } = toRefs(props);

const pageTitle = computed(() => `${user.value.data.username}'s Bins`);

const hasBins = computed(() => bins.value.data.length > 0);
</script>

<template>
    <Head :title="pageTitle" />

    <Layout :user="user" :points="points">
        <!-- bins -->
        <div class="mb-12 space-y-4">
            <div v-if="hasBins" class="flex items-center justify-between">
                <h4 class="font-bold tracking-wide uppercase">Latest Bins</h4>

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
    </Layout>
</template>
