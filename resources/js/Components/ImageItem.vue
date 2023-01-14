<script setup>
import { ref, computed, toRefs } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { formatNumber } from "@/helpers/numbers";
import { toRelativeTime, toLocalizedFormat } from "@/helpers/dates";

const emit = defineEmits(["updateSourceImage", "deleteSourceImage"]);

const props = defineProps({
    image: Object,
});

const { image } = toRefs(props);

const user = computed(() => usePage().props.value.auth.user);

const isLoggedIn = computed(() => user.value !== null);

const updatingDescription = ref(false);
const updateDescription = (
    data = {},
    authCheck = false,
    useInertia = false
) => {
    updatingDescription.value = true;

    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        Inertia.put(route("image.update", image.value.id), data, {
            preserveScroll: true,
            onFinish: () => {
                updatingDescription.value = false;
            },
        });
        return;
    }

    axios
        .put(route("image.update", image.value.id), data)
        .then((res) => {
            emit("updateSourceImage", res.data.data);
        })
        .finally(() => {
            updatingDescription.value = false;
        });
};

const destroying = ref(false);
const destroy = (authCheck = false, useInertia = false) => {
    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        if (confirm("Are you sure you want to delete this image?")) {
            destroying.value = true;

            Inertia.delete(route("image.destroy", image.value.id), {
                preserveScroll: true,
                onFinish: () => {
                    destroying.value = false;
                },
            });
        }
        return;
    }

    if (confirm("Are you sure you want to delete this image?")) {
        destroying.value = true;

        axios
            .delete(route("image.destroy", image.value.id))
            .then((res) => {
                emit("deleteSourceImage", image.value);
            })
            .finally(() => {
                destroying.value = false;
            });
    }
};

const imageItem = computed(() => {
    return {
        // counts
        viewsCount: formatNumber(image.value.views_count),
        favoritesCount: formatNumber(image.value.favorites_count),

        // dates
        localizedCreatedDate: toLocalizedFormat(image.value.created_at),
        relativeCreatedDate: toRelativeTime(image.value.created_at),

        // actions
        updatingDescription: updatingDescription.value,
        updateDescription: updateDescription,
        destroying: destroying.value,
        destroy: destroy,
    };
});
</script>

<template>
    <slot :imageItem="imageItem"></slot>
</template>
