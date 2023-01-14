import { defineStore } from "pinia";

export const useUploadModalStore = defineStore("uploadModal", {
    state: () => ({
        isOpen: false,
        targetBin: null,
        files: [],
    }),
    actions: {
        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
        },
        assignTargetBin(bin) {
            this.targetBin = bin;
        },
        assignFiles(files) {
            this.files = files;
        },
        removeFiles() {
            this.files = [];
        },
    },
});
