<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    docs: Array,
    slug: String,
    content: String,
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Documentation
            </h1>
        </template>
        <div class="flex gap-8 py-6">
            <nav class="w-48 shrink-0 space-y-1">
                <Link
                    v-for="doc in docs"
                    :key="doc.slug"
                    :href="route('docs.show', doc.slug)"
                    :class="[
                        'block rounded-md px-3 py-2 text-sm',
                        doc.slug === slug
                            ? 'bg-gray-100 font-medium text-gray-900 dark:bg-gray-800 dark:text-gray-100'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800',
                    ]"
                >
                    {{ doc.title }}
                </Link>
            </nav>
            <article
                class="prose dark:prose-invert max-w-none flex-1"
                v-html="content"
            />
        </div>
    </AuthenticatedLayout>
</template>
