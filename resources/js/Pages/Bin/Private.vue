<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { onMounted, computed, toRefs } from "vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import "sticksy";
import BinItem from "@/Components/BinItem.vue";
import ImageListItem from "@/Components/ImageListItem.vue";
import CopyText from "@/Components/CopyText.vue";
import BinListItem from "@/Components/BinListItem.vue";
import { HeartIcon, DotsHorizontalIcon } from "@heroicons/vue/solid";

const props = defineProps({
    bin: Object,
    otherBins: Object,
});

const { bin, otherBins } = toRefs(props);

const pageTitle = computed(() => {
    return bin.value.data.title ?? "Untitled";
});

onMounted(() => {
    let stickyElements = Sticksy.initializeAll(".sticky-widget");
    stickyElements.forEach((el) => {
        el.onStateChanged = function (state) {
            if (state === "stuck") {
                el.hardRefresh();
            }
        };
    });
});
</script>

<template>
    <Head :title="pageTitle" />

    <div class="max-w-screen-xl mx-auto">
        <BinItem :bin="bin.data" v-slot="{ binItem }">
            <div
                class="flex flex-col space-y-16 lg:space-y-0 lg:space-x-16 lg:flex-row"
            >
                <!-- content -->
                <div
                    class="flex w-full space-x-0 sm:space-x-8 lg:space-x-16 lg:w-4/5"
                >
                    <!-- sticky sidebar -->
                    <div class="hidden sm:block">
                        <div class="space-y-4 mt-36 sticky-widget">
                            <!-- favorite -->
                            <div
                                class="flex flex-col items-center p-2 space-y-2 border border-base-content/30 rounded-2xl text-base-content/50"
                            >
                                <button
                                    type="button"
                                    @click="binItem.favorite(true, true)"
                                    class="btn btn-ghost btn-circle"
                                    :class="[
                                        bin.data.has_favorited
                                            ? 'text-red-500'
                                            : 'hover:text-base-content',
                                        binItem.favoriting ? 'loading' : '',
                                    ]"
                                >
                                    <HeartIcon
                                        v-if="!binItem.favoriting"
                                        class="w-6 h-6"
                                    />
                                </button>

                                <span
                                    class="cursor-default"
                                    :class="[
                                        bin.data.has_favorited
                                            ? 'text-red-500'
                                            : '',
                                    ]"
                                >
                                    {{ binItem.favoritesCount }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1">
                        <!-- title -->
                        <h1
                            class="text-4xl font-bold leading-tight tracking-wider"
                        >
                            {{ bin.data.title }}
                        </h1>

                        <div
                            class="flex items-center justify-between mt-6 space-x-4"
                        >
                            <!-- meta -->
                            <div class="flex items-center space-x-3">
                                <div>
                                    <div
                                        class="flex items-center space-x-2 text-sm text-base-content/50"
                                    >
                                        <span
                                            :title="
                                                binItem.localizedCreatedDate
                                            "
                                        >
                                            {{ binItem.relativeCreatedDate }}
                                        </span>

                                        <span>&middot;</span>

                                        <span
                                            >{{
                                                binItem.viewsCount
                                            }}
                                            Views</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- actions -->
                            <div>
                                <div class="dropdown dropdown-end">
                                    <label
                                        tabindex="0"
                                        class="btn btn-sm btn-square bg-opacity-80"
                                    >
                                        <DotsHorizontalIcon class="w-4 h-4" />
                                    </label>

                                    <ul
                                        tabindex="0"
                                        class="p-2 mt-1 shadow-xl menu menu-compact dropdown-content bg-base-300 text-base-content rounded-box w-36"
                                    >
                                        <template v-if="binItem.userOwnsBin">
                                            <li>
                                                <Link
                                                    :href="
                                                        route(
                                                            'bin.edit',
                                                            bin.data.id
                                                        )
                                                    "
                                                >
                                                    Edit
                                                </Link>
                                            </li>
                                        </template>

                                        <li>
                                            <CopyText v-slot="{ copy, copied }">
                                                <button
                                                    type="button"
                                                    @click="
                                                        copy(
                                                            route(
                                                                'bin.show',
                                                                bin.data.id
                                                            )
                                                        )
                                                    "
                                                >
                                                    Copy link
                                                </button>
                                            </CopyText>
                                        </li>
                                        <li>
                                            <a
                                                :href="
                                                    route(
                                                        'bin.download',
                                                        bin.data.id
                                                    )
                                                "
                                                target="_blank"
                                            >
                                                <span>Download</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- images -->
                        <div class="mt-8 space-y-8">
                            <div v-for="image in bin.data.images">
                                <ImageListItem :image="image" />
                            </div>
                        </div>

                        <!-- actions -->
                        <div class="sm:hidden">
                            <div
                                class="flex items-center justify-end mt-4 space-x-4"
                            >
                                <!-- favorite -->
                                <div
                                    class="flex items-center justify-center p-2 space-x-2 rounded-2xl text-base-content/50"
                                >
                                    <button
                                        type="button"
                                        @click="binItem.favorite(true, true)"
                                        class="btn btn-ghost btn-circle"
                                        :class="[
                                            bin.data.has_favorited
                                                ? 'text-red-500'
                                                : 'hover:text-base-content',
                                            binItem.favoriting ? 'loading' : '',
                                        ]"
                                    >
                                        <HeartIcon
                                            v-if="!binItem.favoriting"
                                            class="w-6 h-6"
                                        />
                                    </button>

                                    <span
                                        class="cursor-default"
                                        :class="[
                                            bin.data.has_favorited
                                                ? 'text-red-500'
                                                : '',
                                        ]"
                                    >
                                        {{ binItem.favoritesCount }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- sticky sidebar -->
                <div class="w-full lg:w-1/5">
                    <div>
                        <div class="mt-2 sticky-widget">
                            <h4 class="font-bold tracking-wide uppercase">
                                Most Recent Bins
                            </h4>

                            <div class="mt-4 space-y-4">
                                <div v-for="b in otherBins.data">
                                    <BinListItem :bin="b" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </BinItem>
    </div>
</template>
