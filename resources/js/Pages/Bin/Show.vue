<script>
import App from "@/Layouts/App.vue";
export default {
    layout: App,
};
</script>

<script setup>
import { onMounted, toRefs } from "vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import "sticksy";
import BinItem from "@/Components/BinItem.vue";
import CommentsList from "@/Components/CommentsList.vue";
import ImageListItem from "@/Components/ImageListItem.vue";
import CopyText from "@/Components/CopyText.vue";
import {
    ThumbUpIcon,
    ThumbDownIcon,
    ChatAltIcon,
    HeartIcon,
    DotsHorizontalIcon,
} from "@heroicons/vue/solid";
import BinListItem from "@/Components/BinListItem.vue";

const props = defineProps({
    bin: Object,
    otherBins: Object,
});

const { bin, otherBins } = toRefs(props);

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
    <Head :title="bin.data.title" />

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
                            <!-- vote -->
                            <div
                                class="flex flex-col items-center p-2 space-y-2 border border-base-content/30 rounded-2xl text-base-content/50"
                            >
                                <button
                                    type="button"
                                    @click="binItem.upvote(true, true)"
                                    class="btn btn-ghost btn-circle"
                                    :class="[
                                        bin.data.has_upvoted
                                            ? 'text-success'
                                            : 'hover:text-base-content',
                                        binItem.upvoting ? 'loading' : '',
                                    ]"
                                >
                                    <ThumbUpIcon
                                        v-if="!binItem.upvoting"
                                        class="w-6 h-6"
                                    />
                                </button>

                                <span
                                    class="cursor-default"
                                    :class="[
                                        bin.data.has_upvoted
                                            ? 'text-success'
                                            : '',
                                        bin.data.has_downvoted
                                            ? 'text-error'
                                            : '',
                                    ]"
                                    >{{ binItem.votesCount }}</span
                                >

                                <button
                                    type="button"
                                    @click="binItem.downvote(true, true)"
                                    class="btn btn-ghost btn-circle"
                                    :class="[
                                        bin.data.has_downvoted
                                            ? 'text-error'
                                            : 'hover:text-base-content',
                                        binItem.downvoting ? 'loading' : '',
                                    ]"
                                >
                                    <ThumbDownIcon
                                        v-if="!binItem.downvoting"
                                        class="w-6 h-6"
                                    />
                                </button>
                            </div>

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

                            <!-- comments -->
                            <div
                                class="flex flex-col items-center p-2 space-y-2 border border-base-content/30 rounded-2xl text-base-content/50"
                            >
                                <button
                                    type="button"
                                    v-scroll-to="'#comments'"
                                    class="btn btn-ghost btn-circle hover:text-base-content"
                                >
                                    <ChatAltIcon class="w-6 h-6" />
                                </button>

                                <span class="cursor-default">{{
                                    binItem.commentsCount
                                }}</span>
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
                                <div class="avatar">
                                    <div class="w-10 h-10 mask mask-squircle">
                                        <Link
                                            :href="
                                                route(
                                                    'user.bins',
                                                    bin.data.user.username
                                                )
                                            "
                                        >
                                            <img
                                                :src="
                                                    bin.data.user
                                                        .profile_image_url
                                                "
                                            />
                                        </Link>
                                    </div>
                                </div>

                                <div>
                                    <div class="font-extrabold text-primary">
                                        <Link
                                            :href="
                                                route(
                                                    'user.bins',
                                                    bin.data.user.username
                                                )
                                            "
                                        >
                                            {{ bin.data.user.username }}
                                        </Link>
                                    </div>
                                    <div
                                        class="flex items-center space-x-2 text-sm text-base-content/50"
                                    >
                                        <span
                                            :title="
                                                binItem.localizedPublishDate
                                            "
                                        >
                                            {{ binItem.relativePublishDate }}
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
                                <!-- vote -->
                                <div
                                    class="flex items-center justify-center p-2 space-x-2 text-base-content/50"
                                >
                                    <button
                                        type="button"
                                        @click="binItem.upvote(true, true)"
                                        class="btn btn-ghost btn-circle"
                                        :class="[
                                            bin.data.has_upvoted
                                                ? 'text-success'
                                                : 'hover:text-base-content',
                                            binItem.upvoting ? 'loading' : '',
                                        ]"
                                    >
                                        <ThumbUpIcon
                                            v-if="!binItem.upvoting"
                                            class="w-6 h-6"
                                        />
                                    </button>

                                    <span
                                        class="cursor-default"
                                        :class="[
                                            bin.data.has_upvoted
                                                ? 'text-success'
                                                : '',
                                            bin.data.has_downvoted
                                                ? 'text-error'
                                                : '',
                                        ]"
                                        >{{ binItem.votesCount }}</span
                                    >

                                    <button
                                        type="button"
                                        @click="binItem.downvote(true, true)"
                                        class="btn btn-ghost btn-circle"
                                        :class="[
                                            bin.data.has_downvoted
                                                ? 'text-error'
                                                : 'hover:text-base-content',
                                            binItem.downvoting ? 'loading' : '',
                                        ]"
                                    >
                                        <ThumbDownIcon
                                            v-if="!binItem.downvoting"
                                            class="w-6 h-6"
                                        />
                                    </button>
                                </div>

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

                                <!-- comments -->
                                <div
                                    class="flex items-center justify-center p-2 space-x-2 rounded-2xl text-base-content/50"
                                >
                                    <button
                                        type="button"
                                        v-scroll-to="'#comments'"
                                        class="btn btn-ghost btn-circle hover:text-base-content"
                                    >
                                        <ChatAltIcon class="w-6 h-6" />
                                    </button>

                                    <span class="cursor-default">{{
                                        binItem.commentsCount
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- tags -->
                        <div class="flex items-center mt-16 space-x-4">
                            <div v-for="tag in bin.data.tags">
                                <Link
                                    :href="route('tags.show', tag.slug)"
                                    class="normal-case btn btn-accent btn-outline rounded-box"
                                >
                                    {{ tag.name }}
                                </Link>
                            </div>
                        </div>

                        <!-- comments -->
                        <div id="comments" class="pt-4 mt-28">
                            <CommentsList
                                :commentable="bin.data"
                                :commentablePostRoute="
                                    route('bin.comments', bin.data.id)
                                "
                            />
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
