<script setup>
import { ref, computed, toRefs } from "vue";
import { XIcon } from "@heroicons/vue/solid";

const emit = defineEmits(["updateSourceBin"]);

const props = defineProps({
    bin: Object,
});

const { bin } = toRefs(props);

const user = computed(() => usePage().props.value.auth.user);

const isLoggedIn = computed(() => user.value !== null);

const isPublished = computed(() => bin.value.published_at !== null);

const input = ref();

const tagInput = ref();

const tagging = ref(false);
const tag = (data = {}, authCheck = false, useInertia = false) => {
    tagging.value = true;

    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        Inertia.post(route("bin.tag", bin.value.id), data, {
            preserveScroll: true,
            onFinish: () => {
                tagging.value = false;
                tagInput.value = "";
            },
        });
        return;
    }

    axios
        .post(route("bin.tag", bin.value.id), data)
        .then((res) => {
            emit("updateSourceBin", res.data.data);
            tagInput.value = "";
        })
        .finally(() => {
            tagging.value = false;
        });
};

const untagging = ref(false);
const untag = (data = {}, authCheck = false, useInertia = false) => {
    untagging.value = true;

    // if auth is required and user is not logged in, use inertia
    if ((authCheck && !isLoggedIn.value) || useInertia) {
        Inertia.post(route("bin.untag", bin.value.id), data, {
            preserveScroll: true,
            onFinish: () => {
                untagging.value = false;
            },
        });
        return;
    }

    axios
        .post(route("bin.untag", bin.value.id), data)
        .then((res) => {
            emit("updateSourceBin", res.data.data);
        })
        .finally(() => {
            untagging.value = false;
        });
};
</script>

<template>
    <div class="flex flex-wrap gap-3">
        <input
            v-if="!isPublished"
            type="text"
            placeholder="Add tag"
            ref="input"
            v-model="tagInput"
            @keydown.enter.prevent="tag({ tag: tagInput })"
            class="w-full rounded-box input input-bordered max-w-[6rem]"
        />

        <button
            v-if="!isPublished"
            v-for="tag in bin.tags"
            @click="untag({ tag: tag.name })"
            title="Remove tag"
            class="normal-case btn btn-accent group btn-outline rounded-box indicator"
        >
            <span
                class="invisible indicator-item badge badge-primary group-hover:visible group-focus:visible"
            >
                <XIcon class="w-2 h-2" />
            </span>
            {{ tag.name }}
        </button>

        <button
            v-else
            v-for="tag in bin.tags"
            class="normal-case btn btn-accent group btn-outline rounded-box indicator no-animation"
        >
            {{ tag.name }}
        </button>
    </div>
</template>
