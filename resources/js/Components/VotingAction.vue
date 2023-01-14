<script setup>
import { ref, computed, toRefs } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { formatNumber } from "@/helpers/numbers";

const emit = defineEmits(["upvoted", "downvoted"]);

const props = defineProps({
    targetBin: Object,
});

const { targetBin } = toRefs(props);

const user = computed(() => usePage().props.value.auth.user);

const hasVoted = computed(
    () => targetBin.value.data.has_upvoted || targetBin.value.data.has_downvoted
);

const upvoting = ref(false);
const upvote = () => {
    upvoting.value = true;

    if (!user.value) {
        Inertia.post(
            route("bin.upvote", targetBin.value.data.id),
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
        .post(route("bin.upvote", targetBin.value.data.id))
        .then((res) => {
            emit("upvoted", res.data);
        })
        .finally(() => {
            upvoting.value = false;
        });
};

const downvoting = ref(false);
const downvote = () => {
    downvoting.value = true;

    if (!user.value) {
        Inertia.post(
            route("bin.downvote", targetBin.value.data.id),
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
        .post(route("bin.downvote", targetBin.value.data.id))
        .then((res) => {
            emit("downvoted", res.data);
        })
        .finally(() => {
            downvoting.value = false;
        });
};

const votingCount = formatNumber(targetBin.value.data.views_count);
</script>

<template>
    <slot
        :hasVoted="hasVoted"
        :upvoting="upvoting"
        :upvote="upvote"
        :downvoting="downvoting"
        :downvote="downvote"
        :votingCount="votingCount"
    ></slot>
</template>
