<script setup>
import { ref, computed, toRefs } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { formatNumber } from "@/helpers/numbers";
import { toRelativeTime, toLocalizedFormat } from "@/helpers/dates";

const emit = defineEmits([
    "updateSourceBin",
    "deleteSourceBin",
    "publishedSourceBin",
    "unpublishedSourceBin",
]);

const props = defineProps({
    bin: Object,
});

const { bin } = toRefs(props);

const user = computed(() => usePage().props.value.auth.user);

const isLoggedIn = computed(() => user.value !== null);

const userOwnsBin = computed(() => {
    return isLoggedIn.value ? user.value.data.id === bin.value.user.id : false;
});

const updatingTitle = ref(false);
const updateTitle = (data = {}, authCheck = false, useInertia = false) => {
    updatingTitle.value = true;

    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        Inertia.put(route("bin.update", bin.value.id), data, {
            preserveScroll: true,
            onFinish: () => {
                updatingTitle.value = false;
            },
        });
        return;
    }

    axios
        .put(route("bin.update", bin.value.id), data)
        .then((res) => {
            emit("updateSourceBin", res.data.data);
        })
        .finally(() => {
            updatingTitle.value = false;
        });
};

const upvoting = ref(false);
const upvote = (authCheck = false, useInertia = false) => {
    upvoting.value = true;

    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        Inertia.post(
            route("bin.upvote", bin.value.id),
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
        .post(route("bin.upvote", bin.value.id))
        .then((res) => {
            emit("updateSourceBin", res.data.data);
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
            route("bin.downvote", bin.value.id),
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
        .post(route("bin.downvote", bin.value.id))
        .then((res) => {
            emit("updateSourceBin", res.data.data);
        })
        .finally(() => {
            downvoting.value = false;
        });
};

const favoriting = ref(false);
const favorite = (authCheck = false, useInertia = false) => {
    favoriting.value = true;

    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        Inertia.post(
            route("bin.favorite", bin.value.id),
            {},
            {
                preserveScroll: true,
                onFinish: () => {
                    favoriting.value = false;
                },
            }
        );
        return;
    }

    axios
        .post(route("bin.favorite", bin.value.id))
        .then((res) => {
            emit("updateSourceBin", res.data.data);
        })
        .finally(() => {
            favoriting.value = false;
        });
};

const destroying = ref(false);
const destroy = (authCheck = false, useInertia = false) => {
    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        if (confirm("Are you sure you want to delete this bin?")) {
            destroying.value = true;

            Inertia.delete(route("bin.destroy", bin.value.id), {
                preserveScroll: true,
                onFinish: () => {
                    destroying.value = false;
                },
            });
        }
        return;
    }

    if (confirm("Are you sure you want to delete this bin?")) {
        destroying.value = true;

        axios
            .delete(route("bin.destroy", bin.value.id))
            .then((res) => {
                emit("deleteSourceBin", bin.value);
            })
            .finally(() => {
                destroying.value = false;
            });
    }
};

const publishing = ref(false);
const publish = (authCheck = false, useInertia = false) => {
    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        if (confirm("Are you sure you want to publish this bin?")) {
            publishing.value = true;

            Inertia.post(
                route("bin.publish", bin.value.id),
                {},
                {
                    preserveScroll: true,
                    onFinish: () => {
                        publishing.value = false;
                    },
                }
            );
        }
        return;
    }

    if (confirm("Are you sure you want to publish this bin?")) {
        publishing.value = true;

        axios
            .post(route("bin.publish", bin.value.id))
            .then((res) => {
                emit("publishedSourceBin", res.data.data);
            })
            .finally(() => {
                publishing.value = false;
            });
    }
};

const unpublishing = ref(false);
const unpublish = (authCheck = false, useInertia = false) => {
    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        if (confirm("Are you sure you want to unpublish and hide this bin?")) {
            unpublishing.value = true;

            Inertia.post(
                route("bin.unpublish", bin.value.id),
                {},
                {
                    preserveScroll: true,
                    onFinish: () => {
                        unpublishing.value = false;
                    },
                }
            );
        }
        return;
    }

    if (confirm("Are you sure you want to unpublish and hide this bin?")) {
        unpublishing.value = true;

        axios
            .post(route("bin.unpublish", bin.value.id))
            .then((res) => {
                emit("unpublishedSourceBin", res.data.data);
            })
            .finally(() => {
                unpublishing.value = false;
            });
    }
};

const binItem = computed(() => {
    return {
        // counts
        votesCount: formatNumber(bin.value.votes_sum),
        viewsCount: formatNumber(bin.value.views_count),
        commentsCount: formatNumber(bin.value.comments_count),
        favoritesCount: formatNumber(bin.value.favorites_count),

        // dates
        localizedPublishDate: bin.value.published_at
            ? toLocalizedFormat(bin.value.published_at)
            : null,
        relativePublishDate: bin.value.published_at
            ? toRelativeTime(bin.value.published_at)
            : null,
        localizedCreatedDate: bin.value.created_at
            ? toLocalizedFormat(bin.value.created_at)
            : null,
        relativeCreatedDate: bin.value.created_at
            ? toRelativeTime(bin.value.created_at)
            : null,

        // actions
        updatingTitle: updatingTitle.value,
        updateTitle: updateTitle,
        upvoting: upvoting.value,
        upvote: upvote,
        downvoting: downvoting.value,
        downvote: downvote,
        favoriting: favoriting.value,
        favorite: favorite,
        destroying: destroying.value,
        destroy: destroy,
        publishing: publishing.value,
        publish: publish,
        unpublishing: unpublishing.value,
        unpublish: unpublish,

        // user
        userOwnsBin: userOwnsBin.value,
    };
});
</script>

<template>
    <slot :binItem="binItem" />
</template>
