<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const props = defineProps({
    slug: { type: String, required: true },
    title: { type: String, default: '' },
    body: { type: String, default: '' },
});
</script>

<template>
    <AppLayout>

        <Head :title="title || 'Document'" />
        <div class="container mb-4">
            <h1 class="h2 mb-3">{{ title }}</h1>
            <div class="card">
                <div class="card-body">
                    <div v-if="body" v-html="body"></div>
                    <div v-else class="text-muted">—</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
