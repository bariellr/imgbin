<script setup>
import { ref, computed, toRefs } from "vue";
import EmptyState from "@/Components/EmptyState.vue";
import CommentForm from "@/Components/CommentForm.vue";
import CommentCard from "@/Components/CommentCard.vue";
import { PlusIcon, MinusIcon } from "@heroicons/vue/solid";

const props = defineProps({
    commentable: Object,
    commentablePostRoute: String,
});

const { commentable, commentablePostRoute } = toRefs(props);

const comments = computed(() => commentable.value.comments);

const hasComments = computed(() => comments.value.length > 0);

const isShowingAllReplies = ref(false);

const toggleAllReplies = () => {
    isShowingAllReplies.value = !isShowingAllReplies.value;
};
</script>

<template>
    <div>
        <div>
            <CommentForm :postRoute="commentablePostRoute" />
        </div>

        <div class="mt-8 space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="font-bold tracking-wide uppercase">
                    {{ commentable.comments_count }} Comments
                </h4>

                <button
                    type="button"
                    v-if="hasComments"
                    @click="toggleAllReplies()"
                    class="flex items-center space-x-2 normal-case btn btn-sm btn-ghost"
                >
                    <template v-if="!isShowingAllReplies">
                        <PlusIcon class="w-4 h-4" />
                        <span>Expand All</span>
                    </template>
                    <template v-else>
                        <MinusIcon class="w-4 h-4" />
                        <span>Collapse All</span>
                    </template>
                </button>
            </div>

            <div v-if="hasComments" class="space-y-4">
                <div v-for="comment in comments" :key="comment.id">
                    <CommentCard
                        :commentable="commentable"
                        :comment="comment"
                        :isShowingAllReplies="isShowingAllReplies"
                    />
                </div>
            </div>

            <EmptyState v-else class="my-32" />
        </div>
    </div>
</template>
