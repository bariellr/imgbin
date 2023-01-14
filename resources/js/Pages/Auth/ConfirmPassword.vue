<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { ref } from "vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";

const formErrors = ref({});

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => form.reset(),
        onError: (errors) => {
            formErrors.value = errors;
        },
    });
};
</script>

<template>
    <Head title="Confirm Password" />

    <div class="mx-auto max-w-screen-2xl">
        <div class="min-h-16 hero">
            <div class="text-center hero-content">
                <h1 class="text-5xl font-bold leading-tight tracking-wider">
                    Confirm Password
                </h1>
            </div>
        </div>

        <div class="max-w-lg mx-auto">
            <div class="card card-compact">
                <form @submit.prevent="submit" class="space-y-3 card-body">
                    <div class="shadow-lg alert alert-info">
                        <p>
                            This is a secure area of the application. Please
                            confirm your password before continuing.
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

                    <div class="justify-end card-actions">
                        <button
                            type="submit"
                            class="normal-case btn btn-primary"
                            :class="{ loading: form.processing }"
                            :disabled="form.processing"
                        >
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
