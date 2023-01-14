<script setup>
import { useClipboard } from "@vueuse/core";
const { copy, copied } = useClipboard();

const copyText = (text) => {
    copy(text);
};
</script>

<template>
    <slot :copy="copyText" :copied="copied"></slot>
</template>
