<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();
</script>

<template>
    <div class="admin-layout">
        <header class="admin-header">
            <div class="container py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">{{ t('admin.panel_title') }}</h1>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end">
                            <div class="fw-medium">{{ $page.props.auth.user.name }}</div>
                            <small class="opacity-75">{{ $page.props.auth.user.email }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Навигация -->
        <nav class="admin-nav bg-white border-bottom">
            <div class="container py-3">
                <div class="d-flex gap-2 flex-wrap">
                    <Link href="/admin" class="btn btn-outline-primary">
                    {{ t('nav.dashboard') }}
                    </Link>
                    <Link href="/admin/users" class="btn btn-outline-primary">
                    {{ t('admin.users.title') }}
                    </Link>
                    <Link href="/admin/tenders" class="btn btn-outline-primary">
                    {{ t('admin.tenders.title') }}
                    </Link>
                    <Link href="/admin/content" class="btn btn-outline-primary">
                    {{ t('admin.content.title') }}
                    </Link>
                    <Link :href="route('admin.content.static_pages')" class="btn btn-outline-primary">
                    {{ t('admin.content.static_pages.title') }}
                    </Link>
                    <Link href="/admin/ai" class="btn btn-outline-primary">
                    {{ t('admin.ai.title') }}
                    </Link>
                    <Link :href="route('logout')" method="post" class="btn btn-outline-primary">
                    {{ t('nav.logout') }}
                    </Link>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped>
.admin-header {
    background: linear-gradient(120deg, #c8ced6, #cbbde0);
    color: #fff;
}

.admin-header .btn-outline-light {
    border-color: rgba(255, 255, 255, 0.7);
}

main {
    background: #f5f7fb;
}

.admin-nav .btn {
    border-radius: 6px;
    font-weight: 500;
}

.admin-nav .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
