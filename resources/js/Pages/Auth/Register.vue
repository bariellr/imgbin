<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";

const form = useForm({
    name: "",
    username: "",
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
});

const formErrors = ref({});

const submit = () => {
    form.post(route("register"), {
        onError: (errors) => {
            formErrors.value = errors;
        },
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Sign Up" />

    <div class="mx-auto max-w-screen-2xl">
        <div class="min-h-16 hero">
            <div class="text-center hero-content">
                <h1 class="text-5xl font-bold leading-tight tracking-wider">
                    Sign Up
                </h1>
            </div>
        </div>

        <div class="max-w-lg mx-auto">
            <div class="card card-compact">
                <form @submit.prevent="submit" class="space-y-3 card-body">
                    <div class="w-full form-control">
                        <label class="label">
                            <span class="label-text">Name</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Name"
                            class="w-full input input-bordered"
                            required
                        />

                        <p
                            v-if="formErrors.name"
                            class="mt-1 text-sm text-error"
                        >
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
                            required
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

                    <div class="w-full form-control">
                        <label class="label">
                            <span class="label-text">Confirm Password</span>
                        </label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="Confirm Password"
                            class="w-full input input-bordered"
                            required
                        />

                        <p
                            v-if="formErrors.password_confirmation"
                            class="mt-1 text-sm text-error"
                        >
                            {{ formErrors.password_confirmation }}
                        </p>
                    </div>

                    <div class="justify-end card-actions">
                        <Link
                            :href="route('login')"
                            class="normal-case btn btn-link"
                        >
                            Already registered?
                        </Link>

                        <button
                            type="submit"
                            class="normal-case btn btn-primary"
                            :class="{ loading: form.processing }"
                            :disabled="form.processing"
                        >
                            Sign up
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
