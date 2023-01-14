<script setup>
import { computed } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { Link, useForm, usePage } from "@inertiajs/inertia-vue3";
import { debounce } from "lodash";
import AuthRequiredModal from "@/Components/AuthRequiredModal.vue";
import UploadModal from "@/Components/UploadModal.vue";
import Toasts from "@/Components/Toasts.vue";
import {
    PlusIcon,
    SearchIcon,
    ArchiveIcon,
    HeartIcon,
    LogoutIcon,
    ArrowUpIcon,
    UserIcon,
    MenuIcon,
} from "@heroicons/vue/solid";
import { useScroll } from "@vueuse/core";

const { y } = useScroll(document);

const user = computed(() => usePage().props.value.auth.user);

const showToTop = computed(() => y.value > 200);

const searchForm = useForm({
    q: "",
});

const submitSearch = debounce(() => {
    if (!searchForm.q) {
        Inertia.get(route("home.index"));
        return;
    }

    searchForm.get(route("home.index"));
}, 200);
</script>

<template>
    <div class="min-h-screen bg-base-100 text-base-content" ref="layout">
        <!-- nav -->
        <nav class="flex-wrap p-4 mb-4 space-x-4 navbar">
            <div class="flex items-center space-x-2 md:space-x-4">
                <Link
                    :href="route('home.index')"
                    class="text-2xl font-bold tracking-wider normal-case md:text-3xl lg:text-4xl btn btn-ghost hover:bg-transparent"
                    >imgbin</Link
                >
                <Link
                    :href="route('upload.index')"
                    class="hidden space-x-2 normal-case btn btn-primary sm:inline-flex"
                >
                    <PlusIcon class="hidden w-4 h-4 sm:inline-flex" />
                    <span>New Bin</span>
                </Link>
            </div>
            <div class="flex sm:hidden">
                <Link
                    :href="route('upload.index')"
                    class="normal-case btn btn-primary"
                    >New Bin</Link
                >
            </div>
            <div class="flex-1">
                <form
                    @submit.prevent="submitSearch"
                    class="hidden w-full max-w-screen-sm mx-auto sm:block"
                >
                    <div class="form-control">
                        <div class="input-group">
                            <input
                                v-model="searchForm.q"
                                type="text"
                                placeholder="Search"
                                class="w-full input input-bordered"
                            />
                            <button type="submit" class="btn btn-square">
                                <SearchIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <template v-if="user">
                <div class="flex items-center space-x-4">
                    <div class="dropdown dropdown-end">
                        <label
                            tabindex="0"
                            class="normal-case btn-ghost btn hover:bg-transparent"
                        >
                            <span class="hidden mr-4 md:inline-flex">
                                {{ user.data.username }}
                            </span>
                            <div class="avatar">
                                <div class="w-10 h-10 mask mask-squircle">
                                    <img :src="user.data.profile_image_url" />
                                </div>
                            </div>
                        </label>
                        <ul
                            tabindex="0"
                            class="p-2 mt-3 shadow menu menu-compact dropdown-content bg-neutral text-neutral-content rounded-box w-52"
                        >
                            <!-- <li class="menu-title">
                                <span>Choose theme</span>
                            </li>
                            <li class="mb-3">
                                <ThemeSelector
                                    class="py-0 select-sm focus:text-base-100"
                                />
                            </li> -->
                            <li>
                                <Link
                                    :href="
                                        route('user.bins', user.data.username)
                                    "
                                    class="group"
                                >
                                    <ArchiveIcon
                                        class="w-4 h-4 transition-opacity opacity-25 group-hover:opacity-100 group-focus:opacity-100"
                                    />

                                    My Bins
                                </Link>
                            </li>
                            <li>
                                <Link
                                    :href="
                                        route(
                                            'user.favorites',
                                            user.data.username
                                        )
                                    "
                                    class="group"
                                >
                                    <HeartIcon
                                        class="w-4 h-4 transition-opacity opacity-25 group-hover:opacity-100 group-focus:opacity-100"
                                    />

                                    Favorites
                                </Link>
                            </li>
                            <li>
                                <Link
                                    :href="route('account.settings')"
                                    class="group"
                                >
                                    <UserIcon
                                        class="w-4 h-4 transition-opacity opacity-25 group-hover:opacity-100 group-focus:opacity-100"
                                    />

                                    Account Settings
                                </Link>
                            </li>
                            <li>
                                <Link
                                    as="button"
                                    :href="route('logout')"
                                    method="post"
                                    :preserve-state="false"
                                    class="group"
                                >
                                    <LogoutIcon
                                        class="w-4 h-4 transition-opacity opacity-25 group-hover:opacity-100 group-focus:opacity-100"
                                    />

                                    Sign out
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </template>

            <template v-else>
                <div class="items-center hidden space-x-4 sm:flex">
                    <Link
                        :href="route('login')"
                        class="normal-case btn btn-ghost"
                        >Sign in</Link
                    >
                    <Link
                        :href="route('register')"
                        class="normal-case btn btn-primary"
                        >Sign up</Link
                    >
                </div>
                <div class="flex sm:hidden">
                    <div class="dropdown dropdown-end">
                        <label
                            tabindex="0"
                            class="normal-case btn-ghost btn-outline btn"
                        >
                            <MenuIcon class="w-6 h-6" />
                        </label>
                        <ul
                            tabindex="0"
                            class="p-2 mt-3 shadow menu menu-compact dropdown-content bg-neutral text-neutral-content rounded-box w-52"
                        >
                            <li>
                                <Link :href="route('login')" class="group"
                                    >Sign in</Link
                                >
                            </li>
                            <li>
                                <Link :href="route('register')" class="group"
                                    >Sign up</Link
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </template>
        </nav>

        <!-- main -->
        <main class="px-4 py-6 sm:px-8">
            <slot />
        </main>

        <!-- footer -->
        <footer class="h-56 md:h-96"></footer>

        <div v-if="showToTop" class="fixed bottom-6 right-6">
            <button
                type="button"
                v-scroll-to="'#app'"
                class="btn btn-ghost btn-circle hover:text-base-content"
            >
                <ArrowUpIcon class="w-6 h-6" />
            </button>
        </div>
    </div>

    <UploadModal />

    <AuthRequiredModal />

    <Toasts />
</template>
