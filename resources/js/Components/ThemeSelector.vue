<script setup>
import { onMounted, watch } from "vue";
import { useColorMode } from "@vueuse/core";

const mode = useColorMode();

let themes = [
    "light",
    "dark",
    "cupcake",
    "bumblebee",
    "emerald",
    "corporate",
    "synthwave",
    "retro",
    "cyberpunk",
    "valentine",
    "halloween",
    "garden",
    "forest",
    "aqua",
    "lofi",
    "pastel",
    "fantasy",
    "wireframe",
    "black",
    "luxury",
    "dracula",
    "cmyk",
    "autumn",
    "business",
    "acid",
    "lemonade",
    "night",
    "coffee",
    "winter",
];

const changeTheme = (theme) => {
    mode.value = theme;
};

const setTheme = () => {
    let html = document.documentElement;
    html.setAttribute("data-theme", mode.value);
};

watch(mode, () => {
    setTheme();
});

onMounted(() => {
    setTheme();
});
</script>

<template>
    <select
        @change="(e) => changeTheme(e.target.value)"
        class="select select-bordered"
    >
        <option
            v-for="theme in themes"
            :value="theme"
            :selected="theme === mode"
        >
            {{ theme }}
        </option>
    </select>
</template>
