<script setup>
import { computed, toRefs } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import BinItem from "@/Components/BinItem.vue";
import Image from "@/Components/Image.vue";
import {
    ThumbUpIcon,
    ThumbDownIcon,
    EyeIcon,
    ChatAltIcon,
    HeartIcon,
    DotsHorizontalIcon,
    PhotographIcon,
} from "@heroicons/vue/solid";

const emit = defineEmits(["updateSourceBin", "deleteSourceBin"]);

const props = defineProps({
    bin: Object,
});

const { bin } = toRefs(props);

const isPublished = computed(() => bin.value.published_at !== null);

const title = computed(() => bin.value.title ?? "Untitled");
</script>

<template>
    <BinItem
        :bin="bin"
        @updateSourceBin="(data) => emit('updateSourceBin', data)"
        @deleteSourceBin="(data) => emit('deleteSourceBin', data)"
        v-slot="{ binItem }"
    >
        <div class="m-3 shadow-xl card bg-base-300 text-base-content">
            <div class="relative">
                <!-- cover image -->
                <figure v-if="bin.cover_image">
                    <Image
                        :image="bin.cover_image"
                        dimensions="300"
                        :options="{ fit: 'crop' }"
                        :enableLightbox="false"
                        class="object-cover w-full"
                    />
                </figure>

                <!-- gradient overlay -->
                <div
                    class="absolute inset-x-0 bottom-0 h-32 from-base-300/50 to-transparent bg-gradient-to-t"
                ></div>

                <!-- link -->
                <Link
                    :href="
                        isPublished
                            ? route('bin.show', bin.id)
                            : route('bin.private', bin.id)
                    "
                >
                    <span class="absolute inset-0"></span>
                </Link>

                <!-- actions -->
                <div
                    v-if="binItem.userOwnsBin"
                    class="absolute z-20 right-2 top-2"
                >
                    <div class="dropdown dropdown-end">
                        <label
                            tabindex="0"
                            class="btn btn-sm btn-square bg-opacity-80"
                        >
                            <DotsHorizontalIcon class="w-4 h-4" />
                        </label>
                        <ul
                            tabindex="0"
                            class="p-2 mt-1 shadow menu menu-compact dropdown-content bg-base-100 text-base-content rounded-box w-36"
                        >
                            <li>
                                <Link
                                    :href="route('bin.edit', bin.id)"
                                    class="group"
                                >
                                    Edit
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- images count -->
                <div
                    v-if="bin.images_count > 1"
                    class="absolute z-10 flex items-center p-2 space-x-2 cursor-default left-2 bottom-2 text-base-content"
                >
                    <PhotographIcon class="w-6 h-6" />
                    <span>
                        {{ bin.images_count }}
                    </span>
                </div>

                <!-- favorite -->
                <div class="absolute z-10 right-2 bottom-2">
                    <button
                        type="button"
                        @click="binItem.favorite(true)"
                        class="btn btn-circle btn-ghost hover:opacity-100 hover:text-red-400"
                        :class="[
                            bin.has_favorited
                                ? 'opacity-100 text-red-500'
                                : 'opacity-50',
                            binItem.favoriting ? 'loading' : '',
                        ]"
                    >
                        <HeartIcon v-if="!binItem.favoriting" class="w-8 h-8" />
                    </button>
                </div>
            </div>

            <div
                class="p-6 transition-colors border-b-4 card-body"
                :class="[
                    bin.has_upvoted ? 'border-success' : '',
                    bin.has_downvoted ? 'border-error' : '',
                    !bin.has_upvoted && !bin.has_downvoted
                        ? 'border-transparent'
                        : '',
                ]"
            >
                <!-- title -->
                <h4 class="card-title">
                    <Link :href="route('bin.show', bin.id)">
                        {{ title }}
                    </Link>
                </h4>

                <div class="justify-between mt-4 card-actions">
                    <!-- vote -->
                    <div
                        class="flex items-center space-x-2 hover:text-base-content text-base-content/50"
                    >
                        <button
                            type="button"
                            @click="binItem.upvote(true)"
                            class="w-6 h-6 p-0 min-h-6 btn btn-ghost btn-circle"
                            :class="[
                                bin.has_upvoted ? 'text-success' : '',
                                binItem.upvoting ? 'loading' : '',
                            ]"
                        >
                            <ThumbUpIcon
                                v-if="!binItem.upvoting"
                                class="w-4 h-4"
                            />
                        </button>

                        <span
                            class="cursor-default"
                            :class="[
                                bin.has_upvoted ? 'text-success' : '',
                                bin.has_downvoted ? 'text-error' : '',
                            ]"
                            >{{ binItem.votesCount }}</span
                        >

                        <button
                            type="button"
                            @click="binItem.downvote(true)"
                            class="w-6 h-6 p-0 min-h-6 btn btn-ghost btn-circle"
                            :class="[
                                bin.has_downvoted ? 'text-error' : '',
                                binItem.downvoting ? 'loading' : '',
                            ]"
                        >
                            <ThumbDownIcon
                                v-if="!binItem.downvoting"
                                class="w-4 h-4"
                            />
                        </button>
                    </div>

                    <!-- views count -->
                    <div
                        class="flex items-center space-x-2 cursor-default hover:text-base-content text-base-content/50"
                    >
                        <EyeIcon class="w-4 h-4" />
                        <span>
                            {{ binItem.viewsCount }}
                        </span>
                    </div>

                    <!-- comments count -->
                    <div
                        class="flex items-center space-x-2 cursor-default hover:text-base-content text-base-content/50"
                    >
                        <ChatAltIcon class="w-4 h-4" />
                        <span>{{ binItem.commentsCount }}</span>
                    </div>
                </div>
            </div>
        </div>
    </BinItem>
</template>
