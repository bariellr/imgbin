<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { ref, toRefs } from "vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import Layout from "@/Pages/Account/Layout.vue";

const props = defineProps({
    user: Object,
});

const { user } = toRefs(props);

const form = useForm({
    email: user.value.data.email,
    password: "",
    password_confirmation: "",
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
            form.reset("password", "password_confirmation");
        },
    });
};
</script>

<template>
    <Head title="Password & Email" />

    <Layout>
        <div class="card card-compact">
            <form @submit.prevent="submit" class="space-y-3 card-body">
                <div class="w-full form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="Email"
                        class="w-full input input-bordered"
                    />

                    <p v-if="formErrors.email" class="mt-1 text-sm text-error">
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
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Update
                    </button>
                </div>
            </form>
        </div>
    </Layout>
</template>
