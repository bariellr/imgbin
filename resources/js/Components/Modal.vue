<script setup>
import { toRefs } from "vue";
import { Dialog, DialogPanel, DialogTitle } from "@headlessui/vue";
import { XIcon } from "@heroicons/vue/solid";

const emit = defineEmits(["closeModal"]);

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    showCloseButton: {
        type: Boolean,
        default: true,
    },
    autoSize: {
        type: Boolean,
        default: false,
    },
});

const { isOpen, showCloseButton, autoSize } = toRefs(props);

const closeModal = () => {
    emit("closeModal");
};
</script>

<template>
    <Dialog
        static
        :open="isOpen"
        @close="closeModal()"
        class="modal"
        :class="{ 'modal-open': isOpen }"
    >
        <DialogPanel
            class="relative modal-box bg-base-300"
            :class="[
                autoSize ? 'h-auto w-auto max-h-full max-w-full p-0' : 'p-8',
            ]"
        >
            <DialogTitle as="h3" class="mb-4 text-2xl font-bold empty:mb-0">
                <slot name="title" />
            </DialogTitle>

            <div class="flex flex-col">
                <slot />
            </div>

            <div class="mt-4 modal-action empty:mt-0">
                <slot name="actions" />
            </div>

            <button
                v-if="showCloseButton"
                type="button"
                @click="closeModal()"
                class="absolute btn btn-sm btn-circle right-2 top-2"
            >
                <XIcon class="w-4 h-4" />
            </button>
        </DialogPanel>
    </Dialog>
</template>
