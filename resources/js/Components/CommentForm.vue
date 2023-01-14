<script setup>
import { Link, useForm, usePage } from "@inertiajs/inertia-vue3";
import { ref, computed, toRefs, onMounted } from "vue";
import { XIcon } from "@heroicons/vue/solid";

const emit = defineEmits(["close", "commented"]);

const props = defineProps({
    postRoute: String,
    closeable: {
        type: Boolean,
        default: false,
    },
    maxLength: {
        type: Number,
        default: 140,
    },
    autoFocus: {
        type: Boolean,
        default: false,
    },
});

const { postRoute, closeable, maxLength, autoFocus } = toRefs(props);

const user = computed(() => usePage().props.value.auth.user);

const textarea = ref();

const form = useForm({
    comment: "",
});

const commentLengthSum = computed(() => maxLength.value - form.comment.length);

const isValidComment = computed(() => commentLengthSum.value >= 0);

const isSubmitting = ref(false);

const submitError = ref(null);

const submit = () => {
    isSubmitting.value = true;
    submitError.value = null;

    form.post(postRoute.value, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset("comment");
            emit("commented");
        },
        onError: (errors) => {
            submitError.value = errors.comment ?? null;
        },
        onFinish: () => {
            isSubmitting.value = false;
            textarea.value.focus();
        },
    });
};

onMounted(() => {
    if (autoFocus.value) {
        textarea.value.focus();
    }
});
</script>

<template>
    <div class="flex space-x-4">
        <div v-if="user" class="mt-4">
            <div class="avatar">
                <div class="w-10 h-10 mask mask-squircle">
                    <Link :href="route('user.bins', user.data.username)">
                        <img :src="user.data.profile_image_url" />
                    </Link>
                </div>
            </div>
        </div>

        <form
            @submit.prevent="submit()"
            class="flex-1 border shadow-xl card card-compact bg-base-300 border-base-content/10"
        >
            <div class="card-body">
                <div class="relative">
                    <textarea
                        class="w-full resize-none textarea"
                        :class="[!isValidComment ? 'text-error' : '']"
                        :disabled="isSubmitting"
                        placeholder="Write a comment"
                        rows="3"
                        v-model="form.comment"
                        @keydown.enter.prevent="submit()"
                        ref="textarea"
                    ></textarea>

                    <p v-if="submitError" class="mt-1 text-sm text-error">
                        {{ submitError }}
                    </p>

                    <button
                        v-if="closeable"
                        type="button"
                        @click="emit('close')"
                        class="absolute right-1 top-1 btn btn-square btn-sm btn-ghost"
                    >
                        <XIcon class="w-4 h-4" />
                    </button>
                </div>

                <div class="items-center justify-end mt-1 card-actions">
                    <span
                        class="mr-3"
                        :class="[
                            !isValidComment
                                ? 'text-error'
                                : 'text-base-content',
                        ]"
                        >{{ commentLengthSum }}</span
                    >

                    <button
                        type="submit"
                        class="normal-case btn btn-sm btn-primary"
                        :disabled="isSubmitting"
                    >
                        Post
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
