<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { ref } from "vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";

defineProps({
    status: String,
});

const formErrors = ref({});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"), {
        onSuccess: () => {
            form.reset("email");
        },
        onError: (errors) => {
            formErrors.value = errors;
        },
    });
};
</script>

<template>
    <Head title="Forgot Password" />

    <div class="mx-auto max-w-screen-2xl">
        <div class="min-h-16 hero">
            <div class="text-center hero-content">
                <h1 class="text-5xl font-bold leading-tight tracking-wider">
                    Forgot Password
                </h1>
            </div>
        </div>

        <div class="max-w-lg mx-auto">
            <div class="card card-compact">
                <form @submit.prevent="submit" class="space-y-3 card-body">
                    <div v-if="status" class="shadow-lg alert alert-success">
                        <p>{{ status }}</p>
                    </div>

                    <div class="w-full form-control">
                        <label class="label">
                            <span class="label-text">Email</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="Email"
                            class="w-full input input-bordered"
                            required
                        />

                        <p
                            v-if="formErrors.email"
                            class="mt-1 text-sm text-error"
                        >
                            {{ formErrors.email }}
                        </p>
                    </div>

                    <div class="justify-end card-actions">
                        <button
                            type="submit"
                            class="normal-case btn btn-primary"
                            :class="{ loading: form.processing }"
                            :disabled="form.processing"
                        >
                            Email Password Reset Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
