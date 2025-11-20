<script setup>
import { ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    users: Object,
    filters: Object,
    roles: Object,
});

const searchParams = ref({
    role: props.filters.role || '',
    is_blocked: props.filters.is_blocked ?? '',
});

const applyFilters = () => {
    router.get(route('admin.users.index'), searchParams.value, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    searchParams.value = {
        role: '',
        is_blocked: '',
    };
    applyFilters();
};

const blockUser = (user) => {
    if (confirm(t('admin.users.actions.confirm_block'))) {
        router.post(route('admin.users.block', user.id));
    }
};

const unblockUser = (user) => {
    router.post(route('admin.users.unblock', user.id));
};

const deleteUser = (user) => {
    if (confirm(t('admin.users.actions.confirm_delete'))) {
        router.delete(route('admin.users.destroy', user.id));
    }
};

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    return date.toLocaleDateString(page.props.locale || 'ru', {
        day: '2-digit',
        'month': '2-digit',
        year: 'numeric',
    });
};

const userPrimaryRole = (user) => {
    if (Array.isArray(user.role_names) && user.role_names.length > 0) {
        return user.role_names[0];
    }

    return 'supplier';
};

const roleLabel = (user) => {
    const role = userPrimaryRole(user);
    return props.roles[role] || role;
};

const blockedLabel = (isBlocked) => {
    return isBlocked ? t('admin.users.filters.blocked') : t('admin.users.filters.active');
};

const blockedBadgeClass = (isBlocked) => {
    return isBlocked ? 'bg-danger' : 'bg-success';
};
</script>

<template>
    <AdminLayout>
        <div class="admin-users">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.users.index_title') }}</h1>
                <Link :href="route('admin.users.create')" class="btn btn-primary">
                {{ t('admin.users.actions.create') }}
                </Link>
            </div>

            <!-- Фильтры -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ t('admin.users.filters.title', 'Фильтры') }}</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ t('admin.users.filters.role') }}</label>
                            <select v-model="searchParams.role" class="form-select" @change="applyFilters">
                                <option value="">{{ t('admin.users.filters.all') }}</option>
                                <option v-for="(label, value) in roles" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ t('admin.users.filters.status') }}</label>
                            <select v-model="searchParams.is_blocked" class="form-select" @change="applyFilters">
                                <option value="">{{ t('admin.users.filters.all') }}</option>
                                <option value="1">{{ t('admin.users.filters.blocked') }}</option>
                                <option value="0">{{ t('admin.users.filters.active') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button @click="applyFilters" class="btn btn-outline-primary">
                                    {{ t('common.apply', 'Применить') }}
                                </button>
                                <button @click="resetFilters" class="btn btn-outline-secondary">
                                    {{ t('common.reset', 'Сбросить') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблица пользователей -->
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ t('admin.users.table.col_name') }}</th>
                                <th>{{ t('admin.users.table.col_email') }}</th>
                                <th>{{ t('admin.users.table.col_role') }}</th>
                                <th>{{ t('admin.users.table.col_locale') }}</th>
                                <th>{{ t('admin.users.table.col_blocked') }}</th>
                                <th>{{ t('admin.users.table.col_created_at') }}</th>
                                <th class="w-150">{{ t('admin.users.table.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="users.data.length === 0">
                                <td colspan="7" class="text-center text-muted py-4">
                                    {{ t('admin.users.index_empty', 'Пользователи не найдены') }}
                                </td>
                            </tr>
                            <tr v-for="user in users.data" :key="user.id">
                                <td>
                                    {{ user.name }}
                                </td>
                                <td>{{ user.email }}</td>
                                <td>
                                    <span class="badge bg-info text-white">
                                        {{ roleLabel(user) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-light">
                                        {{ user.locale?.toUpperCase() || 'RU' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge text-light" :class="blockedBadgeClass(user.is_blocked)">
                                        {{ blockedLabel(user.is_blocked) }}
                                    </span>
                                </td>
                                <td>{{ formatDate(user.created_at) }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <Link :href="route('admin.users.edit', user.id)"
                                            class="btn btn-sm btn-ghost-primary">
                                        {{ t('admin.users.actions.edit') }}
                                        </Link>

                                        <template v-if="user.is_blocked">
                                            <button @click="unblockUser(user)" class="btn btn-sm btn-ghost-success">
                                                {{ t('admin.users.actions.unblock') }}
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button @click="blockUser(user)" class="btn btn-sm btn-ghost-warning">
                                                {{ t('admin.users.actions.block') }}
                                            </button>
                                        </template>

                                        <button @click="deleteUser(user)" class="btn btn-sm btn-ghost-danger">
                                            {{ t('admin.users.actions.delete') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div v-if="users.links && users.links.length > 3" class="card-footer">
                    <nav class="d-flex justify-content-center">
                        <ul class="pagination mb-0">
                            <li v-for="(link, index) in users.links" :key="index" class="page-item"
                                :class="{ active: link.active, disabled: !link.url }">
                                <Link v-if="link.url" :href="link.url" class="page-link" v-html="link.label" />
                                <span v-else class="page-link" v-html="link.label" />
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.avatar {
    width: 32px;
    height: 32px;
    font-size: 14px;
    font-weight: 600;
}

.w-150 {
    width: 150px;
}

.btn-list {
    display: flex;
    gap: 0.25rem;
}

.btn-ghost-primary {
    color: #0ea5e9;
    background-color: transparent;
    border-color: transparent;
}

.btn-ghost-primary:hover {
    color: #fff;
    background-color: #0ea5e9;
}

.btn-ghost-success {
    color: #10b981;
    background-color: transparent;
    border-color: transparent;
}

.btn-ghost-success:hover {
    color: #fff;
    background-color: #10b981;
}

.btn-ghost-warning {
    color: #f59e0b;
    background-color: transparent;
    border-color: transparent;
}

.btn-ghost-warning:hover {
    color: #fff;
    background-color: #f59e0b;
}

.btn-ghost-danger {
    color: #ef4444;
    background-color: transparent;
    border-color: transparent;
}

.btn-ghost-danger:hover {
    color: #fff;
    background-color: #ef4444;
}
</style>
