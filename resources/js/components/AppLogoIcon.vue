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
        fill="none"
        :class="className"
        v-bind="$attrs"
    >
        <rect x="3" y="12.5" width="4" height="8" rx="1" fill="currentColor" opacity="0.55" />
        <rect x="10" y="8" width="4" height="12.5" rx="1" fill="currentColor" opacity="0.8" />
        <rect x="17" y="3.5" width="4" height="17" rx="1" fill="currentColor" />
        <path
            d="M3.5 10.5 9 6l4.5 3L20.5 3"
            stroke="currentColor"
            stroke-width="1.75"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        <path
            d="M16 3h4.5v4.5"
            stroke="currentColor"
            stroke-width="1.75"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
</template>
