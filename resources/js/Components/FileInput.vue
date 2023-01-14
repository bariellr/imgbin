<script setup>
import { onMounted, ref, toRefs } from "vue";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    modelValue: File | null,
    accept: String,
});

const { modelValue, accept } = toRefs(props);

const input = ref(null);

const browse = () => {
    input.value.click();
};

const change = (e) => {
    emit("update:modelValue", e.target.files[0]);
};

const remove = () => {
    emit("update:modelValue", null);
};
</script>

<template>
    <div class="flex items-center">
        <div v-if="!modelValue">
            <button
                type="button"
                class="normal-case btn btn-sm"
                @click="browse"
            >
                Browse...
            </button>
        </div>

        <div v-else class="flex items-center space-x-4">
            <span>{{ modelValue.name }}</span>

            <button
                type="button"
                class="normal-case btn btn-sm"
                @click="remove"
            >
                Remove
            </button>
        </div>

        <input
            ref="input"
            type="file"
            :accept="accept"
            class="sr-only"
            @change="change"
        />
    </div>
</template>
