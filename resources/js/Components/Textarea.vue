<script setup>
import { nextTick, onMounted, ref } from "vue";

defineProps(["modelValue"]);

const emit = defineEmits(["update:modelValue"]);

const textarea = ref(null);

const resize = async () => {
    await nextTick();
    textarea.value.style.height = "auto";
    textarea.value.style.height = `${textarea.value.scrollHeight}px`;
};

const onInput = ($event) => {
    emit("update:modelValue", $event.target.value);
    resize();
};

onMounted(() => {
    if (textarea.value.hasAttribute("autofocus")) {
        textarea.value.focus();
    }
    resize();
});
</script>

<template>
    <textarea
        :value="modelValue"
        v-text="modelValue"
        @input="onInput"
        ref="textarea"
        rows="1"
    ></textarea>
</template>
