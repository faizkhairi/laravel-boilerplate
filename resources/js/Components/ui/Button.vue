<script setup>
import { cva } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const variants = cva(
    'inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
    {
        variants: {
            variant: {
                default: 'bg-gray-900 text-white hover:bg-gray-800 focus-visible:ring-gray-900 dark:bg-gray-50 dark:text-gray-900 dark:hover:bg-gray-200',
                secondary: 'bg-gray-100 text-gray-900 hover:bg-gray-200 focus-visible:ring-gray-500 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700',
                outline: 'border border-gray-200 bg-transparent hover:bg-gray-100 focus-visible:ring-gray-500 dark:border-gray-700 dark:hover:bg-gray-800',
                ghost: 'hover:bg-gray-100 focus-visible:ring-gray-500 dark:hover:bg-gray-800',
            },
            size: {
                default: 'h-10 px-4 py-2',
                sm: 'h-9 rounded-md px-3',
                lg: 'h-11 rounded-md px-8',
                icon: 'h-10 w-10',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    }
);

const props = defineProps({
    variant: { type: String, default: 'default' },
    size: { type: String, default: 'default' },
    as: { type: String, default: 'button' },
    class: { type: [String, Object, Array], default: '' },
});
</script>

<template>
    <component
        :is="props.as"
        :class="cn(variants({ variant: props.variant, size: props.size }), props.class)"
    >
        <slot />
    </component>
</template>
