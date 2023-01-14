<script setup>
import { ref, computed, toRefs } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { formatNumber } from "@/helpers/numbers";
import { toRelativeTime, toLocalizedFormat } from "@/helpers/dates";

const emit = defineEmits(["updateSourceComment", "deleteSourceComment"]);

const props = defineProps({
    commentable: Object,
    comment: Object,
});

const { commentable, comment } = toRefs(props);

const user = computed(() => usePage().props.value.auth.user);

const isLoggedIn = computed(() => user.value !== null);

const userOwnsCommentable = computed(() => {
    return commentable.value
        ? commentable.value.user.id === comment.value.user.id
        : false;
});

const userOwnsComment = computed(() => {
    return user.value ? user.value.data.id === comment.value.user.id : false;
});

const upvoting = ref(false);
const upvote = (authCheck = false, useInertia = false) => {
    upvoting.value = true;

    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        Inertia.post(
            route("comment.upvote", comment.value.id),
            {},
            {
                preserveScroll: true,
                onFinish: () => {
                    upvoting.value = false;
                },
            }
        );
        return;
    }

    axios
        .post(route("comment.upvote", comment.value.id))
        .then((res) => {
            emit("updateSourceComment", res.data.data);
        })
        .finally(() => {
            upvoting.value = false;
        });
};

const downvoting = ref(false);
const downvote = (authCheck = false, useInertia = false) => {
    downvoting.value = true;

    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        Inertia.post(
            route("comment.downvote", comment.value.id),
            {},
            {
                preserveScroll: true,
                onFinish: () => {
                    downvoting.value = false;
                },
            }
        );
        return;
    }

    axios
        .post(route("comment.downvote", comment.value.id))
        .then((res) => {
            emit("updateSourceComment", res.data.data);
        })
        .finally(() => {
            downvoting.value = false;
        });
};

const destroying = ref(false);
const destroy = (authCheck = false, useInertia = false) => {
    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        if (confirm("Are you sure you want to delete this comment?")) {
            destroying.value = true;

            Inertia.delete(route("comment.destroy", comment.value.id), {
                preserveScroll: true,
                onFinish: () => {
                    destroying.value = false;
                },
            });
        }
        return;
    }

    if (confirm("Are you sure you want to delete this comment?")) {
        destroying.value = true;

        axios
            .delete(route("comment.destroy", comment.value.id))
            .then((res) => {
                emit("deleteSourceComment", comment.value);
            })
            .finally(() => {
                destroying.value = false;
            });
    }
};

const commentItem = computed(() => {
    return {
        // counts
        votesCount: formatNumber(comment.value.votes_sum),

        // dates
        localizedCreatedDate: toLocalizedFormat(comment.value.created_at),
        relativeCreatedDate: toRelativeTime(comment.value.created_at),

        // actions
        upvoting: upvoting.value,
        upvote: upvote,
        downvoting: downvoting.value,
        downvote: downvote,
        destroying: destroying.value,
        destroy: destroy,

        // user
        userOwnsComment: userOwnsComment.value,
        userOwnsCommentable: userOwnsCommentable.value,
    };
});
</script>

<template>
    <slot :commentItem="commentItem"></slot>
</template>
