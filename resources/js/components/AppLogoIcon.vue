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
        viewBox="0 0 32 32"
        :class="className"
        v-bind="$attrs"
    >
        <defs>
            <linearGradient id="storifyBadgeGradient" x1="4" y1="2" x2="28" y2="30" gradientUnits="userSpaceOnUse">
                <stop offset="0" stop-color="#1D1D1D" />
                <stop offset="1" stop-color="#0F0F0F" />
            </linearGradient>
            <linearGradient id="storifySGradient" x1="8.5" y1="8.5" x2="24.5" y2="24.5" gradientUnits="userSpaceOnUse">
                <stop offset="0" stop-color="#1ED760" />
                <stop offset="1" stop-color="#1DB954" />
            </linearGradient>
        </defs>

        <rect x="3" y="3" width="26" height="26" rx="7.5" fill="url(#storifyBadgeGradient)" />
        <rect x="3.5" y="3.5" width="25" height="25" rx="7" stroke="#2A2A2A" stroke-width="1" />
        <path
            d="M22.2 10.2H11.3c-1.7 0-3 1.3-3 3s1.3 3 3 3h8.4c.6 0 1.1.5 1.1 1.1s-.5 1.1-1.1 1.1H9.9v3.4h10.9c2.1 0 3.7-1.7 3.7-3.7s-1.7-3.7-3.7-3.7h-8.4c-.2 0-.4-.2-.4-.4s.2-.4.4-.4h9.8v-3.4Z"
            fill="url(#storifySGradient)"
        />
        <path d="M6 7.4c2.8-2.6 7-3.7 11.3-3.1" stroke="#FFFFFF" stroke-opacity=".08" stroke-linecap="round" />
    </svg>
</template>
