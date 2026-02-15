<script setup>
import { cn } from '@/lib/utils';

defineProps({
    class: { type: [String, Object, Array], default: '' },
});
</script>

<template>
    <div
        :class="cn('rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900', $props.class)"
    >
        <slot />
    </div>
</template>
