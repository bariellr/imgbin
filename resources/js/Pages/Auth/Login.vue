<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const formErrors = ref({});

const submit = () => {
    form.post(route("login"), {
        onError: (errors) => {
            formErrors.value = errors;
        },
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Login" />

    <div class="mx-auto max-w-screen-2xl">
        <div class="min-h-16 hero">
            <div class="text-center hero-content">
                <h1 class="text-5xl font-bold leading-tight tracking-wider">
                    Sign In
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

                    <div class="w-full form-control">
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            placeholder="Password"
                            class="w-full input input-bordered"
                            required
                        />

                        <p
                            v-if="formErrors.password"
                            class="mt-1 text-sm text-error"
                        >
                            {{ formErrors.password }}
                        </p>
                    </div>

                    <div class="form-control">
                        <label
                            class="self-start space-x-3 cursor-pointer label"
                        >
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                checked="checked"
                                class="checkbox checkbox-accent"
                            />
                            <span class="label-text">Remember me</span>
                        </label>
                    </div>

                    <div class="justify-end card-actions">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="normal-case btn btn-link"
                        >
                            Forgot your password?
                        </Link>

                        <button
                            type="submit"
                            class="normal-case btn btn-primary"
                            :class="{ loading: form.processing }"
                            :disabled="form.processing"
                        >
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
