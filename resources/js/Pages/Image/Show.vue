<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { onMounted, toRefs } from "vue";
import "sticksy";
import ImageItem from "@/Components/ImageItem.vue";
import ImageListItem from "@/Components/ImageListItem.vue";
import CopyText from "@/Components/CopyText.vue";
import { DotsHorizontalIcon } from "@heroicons/vue/solid";
import BinListItem from "@/Components/BinListItem.vue";

const props = defineProps({
    image: Object,
    otherBins: Object,
});

const { image, otherBins } = toRefs(props);

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
    <div class="max-w-screen-xl mx-auto">
        <ImageItem :image="image.data" v-slot="{ imageItem }">
            <div
                class="flex flex-col space-y-16 lg:space-y-0 lg:space-x-16 lg:flex-row"
            >
                <!-- content -->
                <div
                    class="flex w-full space-x-0 sm:space-x-8 lg:space-x-16 lg:w-4/5"
                >
                    <div class="flex-1">
                        <!-- title -->
                        <!-- <h1
                            class="text-4xl font-bold leading-tight tracking-wider"
                        >
                            {{ bin.data.title }}
                        </h1> -->

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
                                                imageItem.localizedCreatedDate
                                            "
                                        >
                                            {{ imageItem.relativeCreatedDate }}
                                        </span>

                                        <span>&middot;</span>

                                        <span
                                            >{{
                                                imageItem.viewsCount
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
                                        <li>
                                            <CopyText v-slot="{ copy, copied }">
                                                <button
                                                    type="button"
                                                    @click="
                                                        copy(
                                                            route(
                                                                'image.show',
                                                                image.data.id
                                                            )
                                                        )
                                                    "
                                                >
                                                    {{
                                                        copied
                                                            ? `Copied!`
                                                            : `Copy link`
                                                    }}
                                                </button>
                                            </CopyText>
                                        </li>
                                        <li>
                                            <a
                                                :href="
                                                    route(
                                                        'image.download',
                                                        image.data.id
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
                            <div>
                                <ImageListItem :image="image.data" />
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
        </ImageItem>
    </div>
</template>
