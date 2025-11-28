<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const authUser = computed(() => page.props?.auth?.user || null);
const userRoles = computed(() => authUser.value?.role_names || []);
const hasRole = (role) => userRoles.value.includes(role);
const isAdmin = computed(() => hasRole('admin'));
const isModerator = computed(() => hasRole('moderator'));
const canAccessAdmin = computed(() => isAdmin.value || isModerator.value);

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

const isActiveAny = (paths) => {
    return paths.some((p) => isActive(p));
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
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <Link v-if="canAccessAdmin" href="/admin"
                            :class="['btn btn-sm', isActive('/admin') ? 'btn-dark' : 'btn-outline-dark']">
                        {{ t('nav.dashboard') }}
                        </Link>

                        <div v-if="canAccessAdmin" class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                :class="isActiveAny(['/admin/content', '/admin/content/static-pages', '/admin/content/site-settings']) ? 'btn-dark' : 'btn-outline-dark'">
                                {{ t('admin.content.menu', 'Контент') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <Link class="dropdown-item" href="/admin/content">
                                    {{ t('admin.content.home', 'Переводы') }}
                                    </Link>
                                </li>
                                <li>
                                    <Link class="dropdown-item" :href="route('admin.content.site_settings')">
                                    {{ t('admin.content.site_settings', 'Настройки сайта') }}
                                    </Link>
                                </li>
                                <li>
                                    <Link class="dropdown-item" :href="route('admin.content.static_pages')">
                                    {{ t('admin.content.static_pages.title') }}
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        <div v-if="canAccessAdmin" class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                :class="isActive('/admin/users') || isActive('/admin/suppliers') ? 'btn-dark' : 'btn-outline-dark'">
                                {{ t('admin.users.menu', 'Пользователи') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <Link class="dropdown-item" href="/admin/users">
                                    {{ t('admin.users.title') }}
                                    </Link>
                                </li>
                                <li>
                                    <Link class="dropdown-item" href="/admin/suppliers">
                                    {{ t('admin.users.suppliers', 'Поставщики') }}
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        <div v-if="canAccessAdmin" class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                :class="isActive('/admin/tenders') || isActive('/admin/applications') ? 'btn-dark' : 'btn-outline-dark'">
                                {{ t('admin.tenders.menu', 'Тендеры') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <Link class="dropdown-item" href="/admin/tenders">
                                    {{ t('admin.tenders.all', 'Все тендеры') }}
                                    </Link>
                                </li>
                                <li>
                                    <Link class="dropdown-item" href="/admin/applications">
                                    {{ t('admin.tenders.applications', 'Заявки') }}
                                    </Link>
                                </li>
                            </ul>
                        </div>
                        <Link v-if="isAdmin" :href="route('admin.platform_suppliers.index')"
                            :class="['btn btn-sm', isActive('/admin/platform-suppliers') ? 'btn-dark' : 'btn-outline-dark']">
                        {{ t('admin.menu.platform_suppliers', 'Поставщики (справочник)') }}
                        </Link>

                        <div v-if="isAdmin" class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                :class="isActiveAny(['/admin/backup', '/admin/system-logs']) ? 'btn-dark' : 'btn-outline-dark'">
                                {{ t('admin.menu.backups_logs', 'Копии и Логи') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <Link class="dropdown-item" :href="route('admin.backup.index')">
                                    {{ t('admin.backup.title') }}
                                    </Link>
                                </li>
                                <li>
                                    <Link class="dropdown-item" :href="route('admin.system_logs.index')">
                                    {{ t('admin.system_logs.title') }}
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        <div v-if="isAdmin" class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                :class="isActiveAny(['/admin/ai', '/admin/smtp', '/admin/notification-templates']) ? 'btn-dark' : 'btn-outline-dark'">
                                {{ t('admin.menu.platform_settings', 'Настройки площадки') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <Link class="dropdown-item" :href="route('admin.ai.index')">
                                    {{ t('admin.ai.title') }}
                                    </Link>
                                </li>
                                <li>
                                    <Link class="dropdown-item" :href="route('admin.smtp.index')">
                                    {{ t('admin.smtp.title') }}
                                    </Link>
                                </li>
                                <li>
                                    <Link class="dropdown-item" :href="route('admin.notification_templates.index')">
                                    {{ t('admin.notification_templates.index_title') }}
                                    </Link>
                                </li>
                            </ul>
                        </div>
                        <Link :href="route('logout')" method="post" class="btn btn-sm btn-outline-dark">
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
