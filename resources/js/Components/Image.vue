<script setup>
import { computed, ref, toRefs } from "vue";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    image: Object,
    dimensions: String,
    options: {
        type: Object,
        default: {
            fit: "contain",
        },
    },
    enableLightbox: {
        type: Boolean,
        default: true,
    },
});

const { image, dimensions, options, enableLightbox } = toRefs(props);

const serializeObj = (object) => {
    let str = [];
    for (let p in object) {
        if (object.hasOwnProperty(p)) {
            str.push(
                encodeURIComponent(p) + "=" + encodeURIComponent(object[p])
            );
        }
    }
    return str.join("&");
};

const params = computed(() => {
    if (!dimensions.value) {
        return null;
    }

    let aspectRatio = image.value.width / image.value.height,
        dim = dimensions.value.split("x").map((i) => parseInt(i, 10)),
        p = {};

    p.w = dim[0];

    if (aspectRatio >= 1.45 && !dim[1]) {
        p.h = dim[0];
    } else {
        p.h = dim[1] ?? (p.w / aspectRatio).toFixed();
    }

    return {
        ...p,
        ...options.value,
    };
});

const url = computed(() => {
    return params.value !== null
        ? `${image.value.url}?${serializeObj(params.value)}`
        : image.value.url;
});

const lightboxIsOpen = ref(false);

const openLightbox = () => {
    if (enableLightbox.value === true) {
        lightboxIsOpen.value = true;
    }
};

const closeLightbox = () => {
    if (enableLightbox.value === true) {
        lightboxIsOpen.value = false;
    }
};
</script>

<template>
    <img
        :src="url"
        :width="params?.w ?? image.width"
        :height="params?.h ?? image.height"
        :alt="image.filename"
        @click="openLightbox"
        :class="{ 'cursor-zoom-in': enableLightbox }"
        v-bind="$attrs"
    />

    <Modal
        :isOpen="lightboxIsOpen"
        :showCloseButton="true"
        :autoSize="true"
        @closeModal="closeLightbox"
        class="bg-black"
    >
        <div class="flex items-center justify-center">
            <img
                :src="image.url"
                :width="image.width"
                :height="image.height"
                :alt="image.filename"
                @click="closeLightbox"
                class="max-h-[95vh] max-w-[95vw] cursor-zoom-out w-auto h-auto"
            />
        </div>
    </Modal>
</template>
