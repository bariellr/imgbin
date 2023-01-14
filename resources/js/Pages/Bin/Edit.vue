<script>
import App from "@/Layouts/App.vue";
import { useToastStore } from "@/stores/toast";
export default {
    layout: App,
};
</script>

<script setup>
import { onMounted, ref, computed, onUnmounted } from "vue";
import { Head, Link, usePage } from "@inertiajs/inertia-vue3";
import "sticksy";
import { debounce } from "lodash";
import BinItem from "@/Components/BinItem.vue";
import ImageListItem from "@/Components/ImageListItem.vue";
import Textarea from "@/Components/Textarea.vue";
import TagsInput from "@/Components/TagsInput.vue";
import CopyText from "@/Components/CopyText.vue";
import {
    EyeIcon,
    EyeOffIcon,
    PlusIcon,
    TrashIcon,
    DownloadIcon,
    InformationCircleIcon,
} from "@heroicons/vue/solid";
import { useUploadModalStore } from "@/stores/uploadModal";
import { useToastStore } from "@/stores/toast";

const uploadModal = useUploadModalStore();
const toast = useToastStore();

const props = defineProps({
    bin: Object,
});

const user = computed(() => usePage().props.value.auth.user);

const isLoggedIn = computed(() => user.value !== null);

const bin = ref(props.bin);

const binTitle = ref(bin.value.data.title);

const pageTitle = computed(() => {
    return bin.value.data.title ?? "Untitled";
});

const updateBinTitle = debounce((updateBinItemTitle) => {
    if (
        bin.value.data.title &&
        binTitle.value.trim() === bin.value.data.title.trim()
    ) {
        return;
    }

    updateBinItemTitle({ title: binTitle.value });
}, 500);

const updateSourceBin = (data) => {
    Object.assign(bin.value.data, data);
};

const publishedSourceBin = (data) => {
    Object.assign(bin.value.data, data);
    toast.addSuccessItem("The bin has been published.");
};

const unpublishedSourceBin = (data) => {
    Object.assign(bin.value.data, data);
    toast.addSuccessItem("The bin has been hidden.");
};

const isPublished = computed(() => bin.value.data.published_at !== null);

const isPublishable = computed(
    () => bin.value.data.title !== null && !hasActiveUploads.value
);

const pendingUploads = computed(() =>
    bin.value.data.images.filter(
        (image) =>
            image.uploadData?.isUploaded === false &&
            image.uploadData?.isUploading === false &&
            !image.uploadData?.error
    )
);

const activeUploadsCount = ref(0);

const uploadImages = async (files) => {
    // push files as images to the bin
    await files.map((file) => {
        bin.value.data.images.push({
            uploadData: {
                file: file,
                url: null,
                isUploading: false,
                isUploaded: false,
                isCanceled: false,
                progressSize: 0,
                progressPercent: 0,
                error: null,
                abortController: null,
            },
        });
    });

    return await Promise.allSettled(
        pendingUploads.value.map(async (image) => {
            activeUploadsCount.value++;

            const abortController = new AbortController();

            // image.uploadData.url = await getBlobFromFile(image.uploadData.file);
            image.uploadData.isUploading = true;
            image.uploadData.abortController = abortController;

            let form = new FormData();
            form.append("file", image.uploadData.file);

            await axios
                .post(route("bin.images", bin.value.data.id), form, {
                    signal: abortController.signal,
                    onUploadProgress: (e) => {
                        image.uploadData.progressSize = Math.round(e.loaded);
                        image.uploadData.progressPercent = Math.round(
                            (e.loaded * 100) / e.total
                        );
                    },
                })
                .then((res) => {
                    image.uploadData.isUploaded = true;
                    Object.assign(image, res.data.data);
                })
                .catch((err) => {
                    image.uploadData.error =
                        "Upload failed. Please try again later.";
                    if (err.response?.status === 422) {
                        image.uploadData.error =
                            err.response.data.errors.file[0];
                    }
                    if (err.response.status === 403) {
                        image.uploadData.error = err.response.data.error;
                    }
                })
                .finally(() => {
                    image.uploadData.isUploading = false;
                    // URL.revokeObjectURL(image.uploadData.url);
                    delete image.uploadData.abortController;
                    activeUploadsCount.value--;
                });
        })
    );
};

const hasActiveUploads = computed(() => activeUploadsCount.value > 0);

const stickyElements = ref([]);

onMounted(() => {
    uploadModal.assignTargetBin(bin.value.data);

    if (uploadModal.files.length > 0) {
        uploadImages(uploadModal.files);
    }

    stickyElements.value = Sticksy.initializeAll(".sticky-widget");

    stickyElements.value.forEach((el) => {
        el.onStateChanged = function (state) {
            if (state === "stuck") {
                el.hardRefresh();
            }
        };
    });
});

onUnmounted(() => {
    uploadModal.$reset();
});

uploadModal.$onAction(({ name, store, after }) => {
    after(() => {
        // when files are added to the modal, start file uploads
        if (name == "assignFiles" && store.files.length > 0) {
            uploadImages(uploadModal.files);
        }
    });
});

const removeFromImages = (image) => {
    bin.value.data.images.splice(bin.value.data.images.indexOf(image), 1);
};

const cancelUpload = (image) => {
    if (image.uploadData?.abortController) {
        image.uploadData.abortController.abort();
    }

    removeFromImages(image);

    toast.addSuccessItem("The image upload has been canceled.");
};

const updateSourceImage = (data) => {
    let sourceImage = bin.value.data.images.find((item) => {
        return item.id === data.id;
    });

    Object.assign(sourceImage, data);

    // toast.addSuccessItem("The image has been updated.");
};

const deleteSourceImage = (data) => {
    let sourceImage = bin.value.data.images.find((item) => {
        return item.id === data.id;
    });

    removeFromImages(sourceImage);

    toast.addSuccessItem("The image has been deleted.");
};
</script>

<template>
    <Head :title="pageTitle" />

    <div class="max-w-screen-xl mx-auto">
        <BinItem
            :bin="bin.data"
            @updateSourceBin="updateSourceBin"
            @publishedSourceBin="publishedSourceBin"
            @unpublishedSourceBin="unpublishedSourceBin"
            v-slot="{ binItem }"
        >
            <div
                class="flex flex-col space-y-16 lg:space-y-0 lg:space-x-16 lg:flex-row"
            >
                <!-- content -->
                <div
                    class="flex w-full space-x-0 sm:space-x-8 lg:space-x-16 lg:w-4/5"
                >
                    <div class="flex-1">
                        <!-- title -->
                        <Textarea
                            v-if="!isPublished"
                            v-model="binTitle"
                            @keydown.enter.prevent
                            @input="updateBinTitle(binItem.updateTitle)"
                            placeholder="Give your bin a title"
                            class="w-full p-0 text-4xl font-bold leading-tight tracking-wider bg-transparent border-none resize-none placeholder:text-base-content/30 placeholder:italic focus:border-none focus:outline-none focus:ring-none"
                        ></Textarea>

                        <h1
                            v-else
                            class="text-4xl font-bold leading-tight tracking-wider"
                        >
                            {{ binTitle }}
                        </h1>

                        <!-- images -->
                        <div class="mt-8 space-y-8">
                            <div v-for="image in bin.data.images">
                                <ImageListItem
                                    :image="image"
                                    :editMode="true"
                                    :binIsPublished="isPublished"
                                    @cancelUpload="cancelUpload"
                                    @updateSourceImage="updateSourceImage"
                                    @deleteSourceImage="deleteSourceImage"
                                />
                            </div>
                        </div>

                        <!-- add images button -->
                        <div
                            v-if="!isPublished"
                            class="flex justify-center mt-12"
                        >
                            <button
                                @click="uploadModal.open()"
                                class="mt-6 space-x-2 normal-case btn btn-primary"
                            >
                                <PlusIcon class="w-4 h-4" />
                                <span>Add Images</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- sidebar -->
                <div class="w-full lg:w-1/5">
                    <div class="flex-1">
                        <div class="mt-2 space-y-12 sticky-widget">
                            <!-- community -->
                            <div class="space-y-4">
                                <h4 class="font-bold tracking-wide uppercase">
                                    Community
                                </h4>

                                <div
                                    v-if="isLoggedIn"
                                    class="flex flex-col items-start space-y-4"
                                >
                                    <div class="flex items-center space-x-3">
                                        <button
                                            v-if="!isPublished"
                                            @click="binItem.publish(true)"
                                            :disabled="
                                                binItem.publishing ||
                                                !isPublishable
                                            "
                                            class="space-x-4 normal-case btn btn-success"
                                        >
                                            <span>Publish bin</span>
                                        </button>

                                        <Link
                                            v-else
                                            :href="
                                                route('bin.show', bin.data.id)
                                            "
                                            class="space-x-4 normal-case btn btn-success"
                                        >
                                            <span>View bin</span>
                                        </Link>

                                        <CopyText v-slot="{ copy, copied }">
                                            <button
                                                type="button"
                                                @click="
                                                    copy(
                                                        isPublished
                                                            ? route(
                                                                  'bin.show',
                                                                  bin.data.id
                                                              )
                                                            : route(
                                                                  'bin.private',
                                                                  bin.data.id
                                                              )
                                                    )
                                                "
                                                class="space-x-4 normal-case btn"
                                            >
                                                <span>{{
                                                    copied
                                                        ? `Copied!`
                                                        : `Copy link`
                                                }}</span>
                                            </button>
                                        </CopyText>
                                    </div>

                                    <p
                                        class="flex items-center space-x-2 text-sm text-base-content/60"
                                    >
                                        <EyeIcon
                                            v-if="isPublished"
                                            class="w-4 h-4"
                                        />

                                        <EyeOffIcon v-else class="w-4 h-4" />

                                        <span
                                            >Your bin is currently
                                            <b class="text-primary">{{
                                                isPublished
                                                    ? "Public"
                                                    : "Hidden"
                                            }}</b></span
                                        >
                                    </p>
                                </div>

                                <div
                                    v-else
                                    class="text-sm shadow-lg alert alert-info"
                                >
                                    <InformationCircleIcon
                                        class="w-6 h-6 shrink-0"
                                    />
                                    <p>
                                        You need to be signed in to publish your
                                        bin to the community.
                                    </p>
                                </div>
                            </div>

                            <!-- tags -->
                            <div>
                                <h4 class="font-bold tracking-wide uppercase">
                                    Add Tags
                                </h4>

                                <div
                                    class="flex flex-col items-start mt-4 space-y-2"
                                >
                                    <TagsInput
                                        :bin="bin.data"
                                        @updateSourceBin="updateSourceBin"
                                    />
                                </div>
                            </div>

                            <!-- tools -->
                            <div>
                                <h4 class="font-bold tracking-wide uppercase">
                                    Image Tools
                                </h4>

                                <div
                                    class="flex flex-col items-start mt-4 space-y-2 text-base-content/60"
                                >
                                    <button
                                        v-if="!isPublished"
                                        @click="uploadModal.open()"
                                        class="space-x-4 normal-case bg-transparent hover:text-base-content focus:text-base-content btn btn-ghost hover:bg-transparent focus:bg-transparent"
                                    >
                                        <PlusIcon class="w-4 h-4" />
                                        <span>Add more images</span>
                                    </button>

                                    <a
                                        :href="
                                            route('bin.download', bin.data.id)
                                        "
                                        target="_blank"
                                        class="space-x-4 normal-case bg-transparent hover:text-base-content focus:text-base-content btn btn-ghost hover:bg-transparent focus:bg-transparent disabled:bg-transparent"
                                        :class="{
                                            'btn-disabled': hasActiveUploads,
                                        }"
                                    >
                                        <DownloadIcon class="w-4 h-4" />
                                        <span>Download bin</span>
                                    </a>

                                    <button
                                        @click="binItem.destroy(false, true)"
                                        :disabled="hasActiveUploads"
                                        class="space-x-4 normal-case bg-transparent hover:text-base-content focus:text-base-content btn btn-ghost hover:bg-transparent focus:bg-transparent disabled:bg-transparent"
                                    >
                                        <TrashIcon class="w-4 h-4" />
                                        <span>Delete bin</span>
                                    </button>

                                    <button
                                        v-if="isPublished"
                                        @click="binItem.unpublish(true)"
                                        class="space-x-4 normal-case bg-transparent hover:text-base-content focus:text-base-content btn btn-ghost hover:bg-transparent focus:bg-transparent"
                                    >
                                        <EyeOffIcon class="w-4 h-4" />
                                        <span>Make bin hidden</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </BinItem>
    </div>
</template>
