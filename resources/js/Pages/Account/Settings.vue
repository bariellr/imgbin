<script>
import App from "@/Layouts/App.vue";
import { Inertia } from "@inertiajs/inertia";
export default {
    layout: App,
};
</script>

<script setup>
import { ref, toRefs } from "vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import Layout from "@/Pages/Account/Layout.vue";
import FileInput from "@/Components/FileInput.vue";
import { TrashIcon } from "@heroicons/vue/solid";
import ThemeSelector from "@/Components/ThemeSelector.vue";

const props = defineProps({
    user: Object,
});

const { user } = toRefs(props);

const form = useForm({
    name: user.value.data.name,
    username: user.value.data.username,
    profile_image: null,
});

const formErrors = ref({});

const submit = () => {
    form.post(route("account.update"), {
        preserveScroll: true,
        onError: (errors) => {
            formErrors.value = errors;
        },
        onSuccess: () => {
            formErrors.value = {};
            form.reset("profile_image");
        },
    });
};

const deleteProfileImage = () => {
    if (
        confirm("Are you sure you want to delete your current profile image?")
    ) {
        Inertia.post(route("account.deleteProfileImage"), {}, {});
    }
};
</script>

<template>
    <Head title="Account Settings" />

    <Layout>
        <div class="card card-compact">
            <form @submit.prevent="submit" class="space-y-3 card-body">
                <!-- <div class="w-full form-control">
                    <label class="label">
                        <span class="label-text">Select Theme</span>
                    </label>
                    <ThemeSelector />
                </div> -->

                <div class="w-full form-control">
                    <label class="label">
                        <span class="label-text">Name</span>
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Name"
                        class="w-full input input-bordered"
                    />

                    <p v-if="formErrors.name" class="mt-1 text-sm text-error">
                        {{ formErrors.name }}
                    </p>
                </div>

                <div class="w-full form-control">
                    <label class="label">
                        <span class="label-text">Username</span>
                    </label>
                    <input
                        v-model="form.username"
                        type="text"
                        placeholder="Username"
                        class="w-full input input-bordered"
                    />

                    <p
                        v-if="formErrors.username"
                        class="mt-1 text-sm text-error"
                    >
                        {{ formErrors.username }}
                    </p>
                </div>

                <div class="w-full form-control">
                    <label class="label">
                        <span class="label-text">Profile Image</span>
                    </label>

                    <div
                        v-if="user.data.profile_image"
                        class="flex items-center mt-2 mb-2"
                    >
                        <div class="avatar">
                            <div class="w-60 h-60 mask">
                                <img
                                    :src="user.data.profile_image_url"
                                    class="rounded-md"
                                />
                            </div>

                            <button
                                type="button"
                                @click="deleteProfileImage"
                                title="Delete current profile image"
                                class="absolute flex items-center justify-center right-2 bottom-2 btn btn-error btn-square"
                            >
                                <TrashIcon class="w-6 h-6" />
                            </button>
                        </div>
                    </div>

                    <FileInput
                        v-model="form.profile_image"
                        accept="img/*"
                        class="mt-2"
                    />

                    <p
                        v-if="formErrors.profile_image"
                        class="mt-1 text-sm text-error"
                    >
                        {{ formErrors.profile_image }}
                    </p>
                </div>

                <div class="justify-end card-actions">
                    <button
                        type="submit"
                        class="normal-case btn btn-primary"
                        :class="{ loading: form.processing }"
                        :disabled="form.processing"
                    >
                        Update
                    </button>
                </div>
            </form>
        </div>
    </Layout>
</template>
