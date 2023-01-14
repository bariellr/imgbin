<script setup>
import { inject, ref } from "vue";
import InfiniteScroll from "@/Components/InfiniteScroll.vue";
import Masonry from "@/Components/Masonry.vue";
import MasonryTile from "@/Components/MasonryTile.vue";
import BinCard from "@/Components/BinCard.vue";

const redrawVueMasonry = inject("redrawVueMasonry");

const props = defineProps({
    bins: Object,
});

const bins = ref(props.bins);

const loadMoreBins = async () => {
    if (!bins.value.links.next) {
        return Promise.resolve();
    }

    return axios.get(bins.value.links.next).then((res) => {
        bins.value = {
            ...res.data,
            data: [...bins.value.data, ...res.data.data],
        };
    });
};

const updateSourceBin = (data) => {
    let sourceBin = bins.value.data.find((item) => {
        return item.id === data.id;
    });

    Object.assign(sourceBin, data);
};

const deleteSourceBin = (data) => {
    let sourceBin = bins.value.data.find((item) => {
        return item.id === data.id;
    });

    bins.value.data.splice(bins.value.data.indexOf(sourceBin), 1);

    setTimeout(() => {
        redrawVueMasonry();
    }, 300);
};
</script>

<template>
    <InfiniteScroll
        :loadMoreAction="loadMoreBins"
        loadingText="Loading more bins..."
    >
        <Masonry transition-duration="0.3s" class="-m-3">
            <MasonryTile
                v-for="bin in bins.data"
                :key="bin.id"
                class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/6"
            >
                <BinCard
                    :bin="bin"
                    @updateSourceBin="updateSourceBin"
                    @deleteSourceBin="deleteSourceBin"
                />
            </MasonryTile>
        </Masonry>
    </InfiniteScroll>
</template>
