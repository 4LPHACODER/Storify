<script setup lang="ts">
import { ref } from 'vue';

const props = withDefaults(
    defineProps<{
        src: string;
        alt: string;
        class?: string;
    }>(),
    {
        class: 'h-16 w-16 rounded-md object-cover',
    },
);

const fallback = '/images/product-placeholder.svg';
const imageSrc = ref(props.src || fallback);

const onError = () => {
    imageSrc.value = fallback;
};
</script>

<template>
    <img :src="imageSrc" :alt="alt" :class="props.class" @error="onError" />
</template>
