<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    suppliers: Object,
    filters: Object,
});

const suppliers = computed(() => props.suppliers || { data: [] });

const searchParams = ref({
    search: props.filters.search || '',
    is_blocked: props.filters.is_blocked ?? '',
    requires_moderation: props.filters.requires_moderation ?? false,
});

const applyFilters = () => {
    router.get(route('admin.suppliers.index'), searchParams.value, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    searchParams.value = {
        search: '',
        is_blocked: '',
        requires_moderation: false,
    };
    applyFilters();
};

const block = (id) => {
    router.post(route('admin.users.block', { user: id }));
};

const unblock = (id) => {
    router.post(route('admin.users.unblock', { user: id }));
};

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    return date.toLocaleDateString(page.props.locale || 'ru', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

const blockedLabel = (isBlocked) => {
    return isBlocked
        ? t('admin.users.filters.blocked', 'Заблокированные')
        : t('admin.users.filters.active', 'Активные');
};

const blockedBadgeClass = (isBlocked) => {
    return isBlocked ? 'bg-danger' : 'bg-success';
};

const moderationStatusKey = (supplier) => {
    const pending = supplier.documents_pending_count || 0;
    const approved = supplier.documents_approved_count || 0;
    const rejected = supplier.documents_rejected_count || 0;

    if (!pending && !approved && !rejected) {
        return 'waiting_documents';
    }

    if (rejected > 0) {
        return 'rejected';
    }

    if (pending > 0) {
        return 'in_review';
    }

    if (approved > 0) {
        return 'approved';
    }

    return 'unknown';
};

const moderationLabel = (supplier) => {
    const key = moderationStatusKey(supplier);
    return t(`admin.suppliers.moderation.${key}`);
};

const moderationBadgeClass = (supplier) => {
    const key = moderationStatusKey(supplier);

    if (key === 'waiting_documents') {
        return 'badge bg-secondary text-light';
    }
    if (key === 'in_review') {
        return 'badge bg-info text-light';
    }
    if (key === 'approved') {
        return 'badge bg-success text-light';
    }
    if (key === 'rejected') {
        return 'badge bg-danger text-light';
    }

    return 'badge bg-secondary text-light';
};
</script>

<template>
    <AdminLayout>
        <div class="admin-suppliers">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    {{ t('admin.users.suppliers', 'Поставщики') }}
                </h1>
            </div>

            <!-- Фильтры -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input v-model="searchParams.search" type="text" class="form-control"
                                :placeholder="t('common.search', 'Поиск')" @keyup.enter="applyFilters">
                        </div>
                        <div class="col-md-4">
                            <select v-model="searchParams.is_blocked" class="form-select" @change="applyFilters">
                                <option value="">
                                    {{ t('admin.users.filters.status', 'Статус') }}
                                </option>
                                <option value="1">
                                    {{ t('admin.users.filters.blocked', 'Заблокированные') }}
                                </option>
                                <option value="0">
                                    {{ t('admin.users.filters.active', 'Активные') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <div class="form-check">
                                <input id="requiresModeration" v-model="searchParams.requires_moderation"
                                    class="form-check-input" type="checkbox" @change="applyFilters">
                                <label class="form-check-label" for="requiresModeration">
                                    {{ t('admin.suppliers.filters.requires_moderation', 'Требует модерации') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-outline-primary" @click="applyFilters">
                                    {{ t('common.apply', 'Применить') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary" @click="resetFilters">
                                    {{ t('common.reset', 'Сбросить') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблица поставщиков -->
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ t('common.name', 'Имя') }}</th>
                                <th>Email</th>
                                <th>{{ t('common.status', 'Статус') }}</th>
                                <th>{{ t('admin.suppliers.table.col_moderation', 'Статус модерации') }}</th>
                                <th>{{ t('admin.users.table.col_created_at', 'Дата регистрации') }}</th>
                                <th class="w-150 text-end">
                                    {{ t('common.actions', 'Действия') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="suppliers.data.length === 0">
                                <td colspan="5" class="text-center text-muted py-4">
                                    {{ t('admin.suppliers.index_empty', 'Поставщики не найдены') }}
                                </td>
                            </tr>
                            <tr v-for="u in suppliers.data" :key="u.id">
                                <td>{{ u.name }}</td>
                                <td>{{ u.email }}</td>
                                <td>
                                    <span class="badge text-light" :class="blockedBadgeClass(u.is_blocked)">
                                        {{ blockedLabel(u.is_blocked) }}
                                    </span>
                                </td>
                                <td>
                                    <span :class="moderationBadgeClass(u)">
                                        {{ moderationLabel(u) }}
                                    </span>
                                </td>
                                <td>{{ formatDate(u.created_at) }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap justify-content-end">
                                        <Link :href="route('admin.suppliers.show', { user: u.id })"
                                            class="btn btn-sm btn-outline-secondary">
                                        {{ t('admin.suppliers.actions.view_card', 'Карточка поставщика') }}
                                        </Link>
                                        <button v-if="!u.is_blocked" type="button" class="btn btn-sm btn-ghost-warning"
                                            @click="block(u.id)">
                                            {{ t('admin.users.block', 'Блокировать') }}
                                        </button>
                                        <button v-else type="button" class="btn btn-sm btn-ghost-success"
                                            @click="unblock(u.id)">
                                            {{ t('admin.users.unblock', 'Активировать') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div v-if="suppliers.links && suppliers.links.length > 3" class="card-footer">
                    <nav class="d-flex justify-content-center">
                        <ul class="pagination mb-0">
                            <li v-for="(link, index) in suppliers.links" :key="index" class="page-item"
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
