<script setup>
import { Link } from "@inertiajs/inertia-vue3";
</script>

<template>
    <div class="mx-auto max-w-screen-2xl">
        <div class="mb-6 min-h-16 hero">
            <div class="text-center hero-content">
                <h1 class="text-5xl font-bold leading-tight tracking-wider">
                    Account Settings
                </h1>
            </div>
        </div>

        <div class="justify-center mb-6 uppercase tabs">
            <Link
                :href="route('account.settings')"
                class="tab tab-lg tab-bordered"
                :class="{
                    'tab-active': route().current('account.settings'),
                }"
            >
                Account
            </Link>
            <Link
                :href="route('account.password')"
                class="tab tab-lg tab-bordered"
                :class="{
                    'tab-active': route().current('account.password'),
                }"
            >
                Password & Email
            </Link>
        </div>

        <div class="max-w-md mx-auto">
            <slot />
        </div>
    </div>
</template>
