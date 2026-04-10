<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        :class="className"
        v-bind="$attrs"
    >
        <path
            fill="currentColor"
            d="M3 9.75a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 9.75V18a2.25 2.25 0 0 1-2.25 2.25h-2.25v-5.625a.375.375 0 0 0-.375-.375h-4.5a.375.375 0 0 0-.375.375v5.625H5.25A2.25 2.25 0 0 1 3 18V9.75Zm1.74-5.47A.75.75 0 0 1 5.422 4h13.156a.75.75 0 0 1 .682.43l1.5 3.25a.75.75 0 1 1-1.362.64L18.13 5.5H5.87L4.602 8.32a.75.75 0 0 1-1.362-.64l1.5-3.4Z"
        />
    </svg>
</template>
