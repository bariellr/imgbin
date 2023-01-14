<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Modal from "@/Components/Modal.vue";
import { PhotographIcon } from "@heroicons/vue/solid";
import { useUploadModalStore } from "@/stores/uploadModal";
import { useToastStore } from "@/stores/toast";
import { useDropZone } from "@vueuse/core";

const uploadModal = useUploadModalStore();
const toast = useToastStore();

const dropzoneRef = ref();

const onDropFile = (droppedFiles) => {
    let invalidFiles = 0;
    let files = droppedFiles.filter((item) => {
        let isValid = item.type.includes("image/");
        if (!isValid) invalidFiles++;
        return isValid;
    });

    if (invalidFiles) {
        toast.addErrorItem("Sorry, you can't upload that file type.");
    }

    prepareUpload(files);
};

const { isOverDropZone } = useDropZone(dropzoneRef, onDropFile);

const onBrowseFile = (e) => {
    let invalidFiles = 0;
    let files = [...e.target.files].filter((item) => {
        let isValid = item.type.includes("image/");
        if (!isValid) invalidFiles++;
        return isValid;
    });

    e.target.value = "";

    if (invalidFiles) {
        toast.addErrorItem("Sorry, you can't upload that file type.");
    }

    prepareUpload(files);
};

const prepareUpload = (files) => {
    if (!files.length) {
        uploadModal.close();
        return;
    }

    if (!uploadModal.targetBin) {
        axios.post(route("bin.store")).then((res) => {
            Inertia.get(
                route("bin.edit", res.data.data.id),
                {},
                {
                    onFinish: () => {
                        uploadModal.assignFiles(files);
                        uploadModal.close();
                    },
                }
            );
        });
        return;
    }

    uploadModal.assignFiles(files);
    uploadModal.close();
};
</script>

<template>
    <Modal :isOpen="uploadModal.isOpen" @closeModal="uploadModal.close()">
        <template #title> Upload your images </template>

        <div
            class="flex flex-col items-center justify-center px-4 py-16 card card-compact text-base-content"
            ref="dropzoneRef"
        >
            <PhotographIcon class="w-12 h-12" />
            <p class="mt-4 text-lg text-center">
                Drop your images here or
                <label
                    class="inline-block tracking-wide underline cursor-pointer text-accent underline-offset-2"
                >
                    browse
                    <input
                        @change="onBrowseFile"
                        type="file"
                        class="sr-only"
                        multiple
                        accept="img/*"
                    />
                </label>
            </p>
        </div>
    </Modal>
</template>
