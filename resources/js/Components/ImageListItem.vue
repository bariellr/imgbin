<script setup>
import { ref, computed, toRefs } from "vue";
import { debounce } from "lodash";
import ImageItem from "@/Components/ImageItem.vue";
import Image from "@/Components/Image.vue";
import Textarea from "@/Components/Textarea.vue";
import CopyText from "@/Components/CopyText.vue";
import { DotsHorizontalIcon } from "@heroicons/vue/solid";

const emit = defineEmits([
    "cancelUpload",
    "updateSourceImage",
    "deleteSourceImage",
]);

const props = defineProps({
    image: Object,
    binIsPublished: {
        type: Boolean,
        default: false,
    },
    editMode: {
        type: Boolean,
        default: false,
    },
});

const { image, binIsPublished, editMode } = toRefs(props);

const description = ref(image.value.description);

const updateDescription = debounce((updateImageDescription) => {
    if (
        image.value.description &&
        description.value.trim() === image.value.description.trim()
    ) {
        return;
    }

    updateImageDescription({ description: description.value });
}, 500);

const isReady = computed(() => image.value.url && image.value.url !== null);

const isUploading = computed(
    () => !isReady.value && image.value.uploadData?.isUploading
);

const uploadFailed = computed(() => image.value.uploadData?.error);

const hasDescription = computed(
    () => image.value.description && image.value.description !== null
);
</script>

<template>
    <ImageItem
        :image="image"
        @updateSourceImage="(data) => emit('updateSourceImage', data)"
        @deleteSourceImage="(data) => emit('deleteSourceImage', data)"
        v-slot="{ imageItem }"
    >
        <div class="shadow-xl card bg-base-300">
            <!-- image -->
            <div class="bg-black">
                <!-- ready -->
                <figure v-if="isReady" class="relative group">
                    <Image :image="image" class="object-cover max-w-full" />

                    <div
                        class="absolute z-20 hidden right-2 top-2 group-hover:block"
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
                                    <a
                                        :href="route('image.show', image.id)"
                                        target="_blank"
                                    >
                                        <span>View</span>
                                    </a>
                                </li>
                                <li>
                                    <CopyText v-slot="{ copy, copied }">
                                        <button
                                            type="button"
                                            @click="
                                                copy(
                                                    route(
                                                        'image.show',
                                                        image.id
                                                    )
                                                )
                                            "
                                        >
                                            {{
                                                copied ? `Copied!` : `Copy link`
                                            }}
                                        </button>
                                    </CopyText>
                                </li>
                                <li>
                                    <a
                                        :href="
                                            route('image.download', image.id)
                                        "
                                        target="_blank"
                                    >
                                        <span>Download</span>
                                    </a>
                                </li>
                                <li v-if="editMode && !binIsPublished">
                                    <button
                                        type="button"
                                        @click="imageItem.destroy()"
                                    >
                                        Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </figure>

                <!-- uploading -->
                <div v-if="isUploading" class="relative pb-[50%]">
                    <div
                        class="absolute inset-0 flex items-center justify-center p-4"
                    >
                        <div
                            class="flex flex-col items-center justify-center space-y-12"
                        >
                            <div
                                class="radial-progress text-primary"
                                :style="`--value:${image.uploadData.progressPercent};`"
                            >
                                {{ `${image.uploadData.progressPercent}%` }}
                            </div>

                            <button
                                @click="emit('cancelUpload', image)"
                                class="normal-case btn btn-error"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- upload failed -->
                <div v-if="uploadFailed" class="relative pb-[50%]">
                    <div
                        class="absolute inset-0 flex items-center justify-center p-4"
                    >
                        <div
                            class="flex flex-col items-center justify-center space-y-12"
                        >
                            <p class="text-lg">{{ image.uploadData.error }}</p>

                            <button
                                @click="emit('cancelUpload', image)"
                                class="normal-case btn btn-error"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- description -->
            <template v-if="editMode && isReady">
                <div class="relative card-body">
                    <Textarea
                        v-model="description"
                        :disabled="isUploading"
                        placeholder="Add a description"
                        @input="updateDescription(imageItem.updateDescription)"
                        class="w-full p-0 bg-transparent border-none resize-none placeholder:text-base-content/30 focus:border-none focus:outline-none focus:ring-none"
                    ></Textarea>
                </div>
            </template>

            <div v-else-if="hasDescription" class="card-body">
                <div
                    class="prose prose-ol:my-5 prose-ul:my-5 md:prose-lg lg:prose-xl max-w-none"
                    v-html="image.description_html"
                ></div>
            </div>
        </div>
    </ImageItem>
</template>
