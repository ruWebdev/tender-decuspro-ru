<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

// helper to check if current URL matches a given path
const isActive = (path) => {
    try {
        const current = page.url || (page?.props?.ziggy?.location ?? '');
        // Normalize URLs without query/hash
        const currentPath = current.replace(/[?#].*$/, '');
        // Treat exact match, and nested paths as active
        if (path === '/admin') {
            return currentPath === '/admin';
        }
        return currentPath.startsWith(path);
    } catch (e) {
        return false;
    }
};

// Content section is active for /admin/content and its subsections
// EXCEPT the dedicated static-pages editor route.
const isActiveContent = () => {
    try {
        const current = page.url || (page?.props?.ziggy?.location ?? '');
        const p = current.replace(/[?#].*$/, '');
        if (p === '/admin/content') return true;
        if (p.startsWith('/admin/content/static-pages')) return false;
        return (
            p.startsWith('/admin/content/pages') ||
            p.startsWith('/admin/content/articles') ||
            p.startsWith('/admin/content/news')
        );
    } catch (e) {
        return false;
    }
};
</script>

<template>
    <div class="admin-layout">
        <header class="admin-header">
            <div class="container py-4 text-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2 flex-wrap">
                        <Link href="/admin" :class="['btn', isActive('/admin') ? 'btn-dark' : 'btn-outline-dark']">
                        {{ t('nav.dashboard') }}
                        </Link>
                        <Link href="/admin/users"
                            :class="['btn', isActive('/admin/users') ? 'btn-dark' : 'btn-outline-dark']">
                        {{ t('admin.users.title') }}
                        </Link>
                        <Link href="/admin/tenders"
                            :class="['btn', isActive('/admin/tenders') ? 'btn-dark' : 'btn-outline-dark']">
                        {{ t('admin.tenders.title') }}
                        </Link>
                        <Link href="/admin/content"
                            :class="['btn', isActiveContent() ? 'btn-dark' : 'btn-outline-dark']">
                        {{ t('admin.content.title') }}
                        </Link>
                        <Link :href="route('admin.content.static_pages')"
                            :class="['btn', isActive('/admin/content/static-pages') ? 'btn-dark' : 'btn-outline-dark']">
                        {{ t('admin.content.static_pages.title') }}
                        </Link>
                        <Link href="/admin/ai"
                            :class="['btn', isActive('/admin/ai') ? 'btn-dark' : 'btn-outline-dark']">
                        {{ t('admin.ai.title') }}
                        </Link>
                        <Link href="/admin/smtp"
                            :class="['btn', isActive('/admin/smtp') ? 'btn-dark' : 'btn-outline-dark']">
                        {{ t('admin.smtp.title') }}
                        </Link>
                        <Link :href="route('logout')" method="post" class="btn btn-outline-dark">
                        {{ t('nav.logout') }}
                        </Link>
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
