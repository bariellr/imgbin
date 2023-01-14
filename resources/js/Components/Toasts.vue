<script setup>
import { computed, onMounted, watch } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { useToastStore } from "@/stores/toast";
import {
    XIcon,
    CheckCircleIcon,
    QuestionMarkCircleIcon,
    ExclamationCircleIcon,
} from "@heroicons/vue/solid";

const toast = useToastStore();

const pageToast = computed(() => usePage().props.value.toast);

watch(pageToast, (value) => {
    if (value) {
        toast.addFromSession(value);
    }
});

onMounted(() => {
    if (pageToast.value) {
        toast.addFromSession(value);
    }
});

const types = {
    normal: "",
    info: "alert-info",
    success: "alert-success",
    warning: "alert-warning",
    error: "alert-error",
};

const icons = {
    normal: QuestionMarkCircleIcon,
    info: QuestionMarkCircleIcon,
    success: CheckCircleIcon,
    warning: ExclamationCircleIcon,
    error: ExclamationCircleIcon,
};
</script>

<template>
    <div class="fixed z-50 bottom-6 inset-x-6">
        <transition-group
            tag="div"
            class="flex flex-col space-y-4"
            enter-active-class="duration-300 ease-out"
            enter-from-class="translate-y-10 opacity-0"
            enter-to-class="opacity-100 -translate-y-0"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100 -translate-y-0"
            leave-to-class="translate-y-10 opacity-0"
        >
            <div
                v-for="item in toast.items"
                :key="item.id"
                class="shadow-lg alert w-fit"
                :class="[types[item.type]]"
            >
                <div class="flex">
                    <!-- icon -->
                    <component
                        :is="icons[item.type]"
                        class="w-5 h-5"
                    ></component>

                    <!-- message -->
                    <span class="flex-1">{{ item.message }}</span>

                    <!-- close -->
                    <div class="flex-none">
                        <button
                            @click="toast.removeItem(item)"
                            class="normal-case btn btn-square btn-sm btn-ghost"
                        >
                            <XIcon class="w-4 h-4" />
                            <span class="sr-only">Dismiss</span>
                        </button>
                    </div>
                </div>
            </div>
        </transition-group>
    </div>
</template>
