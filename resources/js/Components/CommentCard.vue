<script setup>
import { ref, computed, toRefs, watch } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import CommentForm from "@/Components/CommentForm.vue";
import CommentItem from "@/Components/CommentItem.vue";
import {
    ThumbUpIcon,
    ThumbDownIcon,
    PlusIcon,
    MinusIcon,
    ChatAltIcon,
    DotsHorizontalIcon,
} from "@heroicons/vue/solid";

const emit = defineEmits(["mutateSource"]);

const props = defineProps({
    commentable: Object,
    comment: Object,
    isShowingAllReplies: Boolean,
});

const { commentable, comment, isShowingAllReplies } = toRefs(props);

const hasReplies = computed(() => comment.value.replies_count > 0);

const isShowingReplies = ref(isShowingAllReplies.value);

watch(isShowingAllReplies, (value) => {
    isShowingReplies.value = value;
});

const toggleReplies = () => {
    isShowingReplies.value = !isShowingReplies.value;
};

const showReplies = () => {
    isShowingReplies.value = true;
};

const closeReplies = () => {
    isShowingReplies.value = false;
};

const isShowingForm = ref(false);

const showForm = () => {
    isShowingForm.value = true;
};

const closeForm = () => {
    isShowingForm.value = false;
};
</script>

<template>
    <CommentItem
        :commentable="commentable"
        :comment="comment"
        v-slot="{ commentItem }"
    >
        <div
            class="relative shadow-xl cursor-pointer card card-compact bg-base-300 text-base-content"
            @click.stop="toggleReplies()"
        >
            <div class="flex flex-col space-y-2 card-body">
                <div class="flex items-center justify-between space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="avatar">
                            <div class="w-6 h-6 mask mask-squircle">
                                <Link
                                    :href="
                                        route(
                                            'user.bins',
                                            comment.user.username
                                        )
                                    "
                                >
                                    <img
                                        :src="comment.user.profile_image_url"
                                    />
                                </Link>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 text-sm">
                                <div class="font-extrabold text-primary">
                                    <Link
                                        @click.stop
                                        :href="
                                            route(
                                                'user.bins',
                                                comment.user.username
                                            )
                                        "
                                    >
                                        {{ comment.user.username }}
                                    </Link>
                                </div>

                                <span
                                    v-if="commentItem.userOwnsCommentable"
                                    class="font-bold badge badge-sm badge-accent"
                                >
                                    OP
                                </span>

                                <span>&middot;</span>

                                <span :title="commentItem.localizedCreatedDate">
                                    {{ commentItem.relativeCreatedDate }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- owner actions -->
                    <div v-if="commentItem.userOwnsComment">
                        <div class="dropdown dropdown-end">
                            <label
                                tabindex="0"
                                class="btn btn-sm btn-square bg-opacity-80"
                                @click.stop
                            >
                                <DotsHorizontalIcon class="w-4 h-4" />
                            </label>
                            <ul
                                tabindex="0"
                                class="p-2 mt-1 shadow menu menu-compact dropdown-content bg-base-100 text-base-content rounded-box w-36"
                            >
                                <li>
                                    <button
                                        type="button"
                                        @click="commentItem.destroy(true, true)"
                                    >
                                        Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="prose md:prose-lg lg:prose-xl max-w-none">
                    {{ comment.content }}
                </div>

                <div class="flex items-center justify-between min-h-8">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex items-center space-x-2 hover:text-base-content text-base-content/50"
                        >
                            <button
                                type="button"
                                @click.stop="commentItem.upvote(true, true)"
                                class="w-6 h-6 p-0 min-h-6 btn btn-ghost btn-circle"
                                :class="[
                                    comment.has_upvoted ? 'text-success' : '',
                                    commentItem.upvoting ? 'loading' : '',
                                ]"
                            >
                                <ThumbUpIcon
                                    v-if="!commentItem.upvoting"
                                    class="w-4 h-4"
                                />
                            </button>

                            <span
                                class="cursor-default"
                                :class="[
                                    comment.has_upvoted ? 'text-success' : '',
                                    comment.has_downvoted ? 'text-error' : '',
                                ]"
                                >{{ commentItem.votesCount }}</span
                            >

                            <button
                                type="button"
                                @click.stop="commentItem.downvote(true, true)"
                                class="w-6 h-6 p-0 min-h-6 btn btn-ghost btn-circle"
                                :class="[
                                    comment.has_downvoted ? 'text-error' : '',
                                    commentItem.downvoting ? 'loading' : '',
                                ]"
                            >
                                <ThumbDownIcon
                                    v-if="!commentItem.downvoting"
                                    class="w-4 h-4"
                                />
                            </button>
                        </div>

                        <div
                            class="flex items-center justify-between space-x-2 hover:text-base-content text-base-content/50"
                        >
                            <button
                                type="button"
                                v-if="hasReplies"
                                @click.stop="toggleReplies()"
                                class="flex items-center space-x-2 normal-case btn btn-sm btn-ghost"
                            >
                                <template v-if="!isShowingReplies">
                                    <PlusIcon class="w-4 h-4" />
                                    <span>{{
                                        `${comment.replies_count} replies`
                                    }}</span>
                                </template>
                                <template v-else>
                                    <MinusIcon class="w-4 h-4" />
                                    <span>Collapse replies</span>
                                </template>
                            </button>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between ml-auto space-x-2 hover:text-base-content text-base-content/50"
                    >
                        <button
                            type="button"
                            v-if="!isShowingForm"
                            @click.stop="showForm()"
                            class="flex items-center space-x-2 normal-case btn btn-sm btn-secondary"
                        >
                            <ChatAltIcon class="w-4 h-4" />
                            <span>Reply</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="(hasReplies && isShowingReplies) || isShowingForm"
            class="mt-4 ml-8 space-y-4"
        >
            <div v-if="isShowingForm">
                <CommentForm
                    :postRoute="route('comment.replies', comment.id)"
                    :closeable="true"
                    :autoFocus="true"
                    @close="closeForm()"
                    @commented="showReplies()"
                />
            </div>

            <div
                v-if="hasReplies && isShowingReplies"
                v-for="reply in comment.replies"
                :key="reply.id"
            >
                <CommentCard
                    :commentable="commentable"
                    :comment="reply"
                    :isShowingAllReplies="isShowingAllReplies"
                />
            </div>
        </div>
    </CommentItem>
</template>
