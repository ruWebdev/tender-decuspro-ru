<script setup>
import { ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    tenders: Object,
    filters: Object,
    customers: Array,
    statuses: Object,
});

const searchParams = ref({
    status: props.filters.status || '',
    is_finished: props.filters.is_finished ?? '',
    customer_id: props.filters.customer_id || '',
});

const applyFilters = () => {
    router.get(route('admin.tenders.index'), searchParams.value, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    searchParams.value = {
        status: '',
        is_finished: '',
        customer_id: '',
    };
    applyFilters();
};

const deleteTender = (tender) => {
    if (confirm(t('admin.tenders.actions.confirm_delete'))) {
        router.delete(route('admin.tenders.destroy', tender.id));
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

const statusLabel = (status) => {
    return props.statuses[status] || status;
};

const statusBadgeClass = (status) => {
    switch (status) {
        case 'open':
            return 'bg-success';
        case 'closed':
            return 'bg-secondary';
        case 'review':
            return 'bg-warning';
        default:
            return 'bg-dark';
    }
};

const finishedBadgeClass = (isFinished) => {
    return isFinished ? 'bg-info' : 'bg-primary';
};

const finishedLabel = (isFinished) => {
    return isFinished ? t('admin.tenders.filters.finished') : t('admin.tenders.filters.active');
};

const customerLabel = (customerId) => {
    const customer = props.customers.find(c => c.id === customerId);
    return customer ? customer.name : t('common.unknown', 'Неизвестно');
};
</script>

<template>
    <AdminLayout>
        <div class="admin-tenders">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.tenders.index_title') }}</h1>
                <Link :href="route('admin.tenders.create')" class="btn btn-primary">
                {{ t('admin.tenders.actions.create') }}
                </Link>
            </div>

            <!-- Фильтры -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ t('admin.tenders.filters.title', 'Фильтры') }}</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ t('admin.tenders.filters.status') }}</label>
                            <select v-model="searchParams.status" class="form-select" @change="applyFilters">
                                <option value="">{{ t('admin.tenders.filters.all') }}</option>
                                <option v-for="(label, value) in statuses" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ t('admin.tenders.filters.is_finished') }}</label>
                            <select v-model="searchParams.is_finished" class="form-select" @change="applyFilters">
                                <option value="">{{ t('admin.tenders.filters.all') }}</option>
                                <option value="1">{{ t('admin.tenders.filters.finished') }}</option>
                                <option value="0">{{ t('admin.tenders.filters.active') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ t('admin.tenders.filters.customer') }}</label>
                            <select v-model="searchParams.customer_id" class="form-select" @change="applyFilters">
                                <option value="">{{ t('admin.tenders.filters.all') }}</option>
                                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                    {{ customer.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
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

            <!-- Таблица тендеров -->
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ t('admin.tenders.table.col_title') }}</th>
                                <th>{{ t('admin.tenders.table.col_status') }}</th>
                                <th>{{ t('admin.tenders.table.col_valid_until') }}</th>
                                <th>{{ t('admin.tenders.table.col_created_at') }}</th>
                                <th>{{ t('admin.tenders.table.col_items') }}</th>
                                <th>{{ t('admin.tenders.table.col_chat') }}</th>
                                <th class="w-150">{{ t('admin.tenders.table.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="tenders.data.length === 0">
                                <td colspan="7" class="text-center text-muted py-4">
                                    {{ t('admin.tenders.index_empty', 'Тендеры не найдены') }}
                                </td>
                            </tr>
                            <tr v-for="tender in tenders.data" :key="tender.id">
                                <td>
                                    <div style="max-width: 200px;">
                                        <strong>{{ tender.title }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge me-1 text-light" :class="statusBadgeClass(tender.status)">
                                        {{ statusLabel(tender.status) }}
                                    </span>
                                    <span class="badge text-light" :class="finishedBadgeClass(tender.is_finished)">
                                        {{ finishedLabel(tender.is_finished) }}
                                    </span>
                                </td>
                                <td>{{ formatDate(tender.valid_until) }}</td>
                                <td>{{ formatDate(tender.created_at) }}</td>
                                <td>
                                    <span class="badge bg-info text-light">
                                        {{ tender.items_count || 0 }}
                                    </span>
                                </td>
                                <td>
                                    <div v-if="tender.chats_count && tender.chats_count > 0"
                                        class="position-relative d-inline-block">
                                        <span class="badge text-light"
                                            :class="tender.chats_with_unread_count > 0 ? 'bg-danger chat-indicator-blink' : 'bg-success'">
                                            <i class="ti ti-message-circle"></i>
                                        </span>
                                        <span v-if="tender.chats_with_unread_count > 0"
                                            class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                                    </div>
                                    <span v-else class="text-muted">-</span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <Link :href="route('admin.tenders.show', tender.id)"
                                            class="btn btn-sm btn-ghost-primary">
                                        {{ t('admin.tenders.actions.show') }}
                                        </Link>
                                        <Link :href="route('admin.tenders.edit', tender.id)"
                                            class="btn btn-sm btn-ghost-warning">
                                        {{ t('admin.tenders.actions.edit') }}
                                        </Link>
                                        <button @click="deleteTender(tender)" class="btn btn-sm btn-ghost-danger">
                                            {{ t('admin.tenders.actions.delete') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div v-if="tenders.links && tenders.links.length > 3" class="card-footer">
                    <nav class="d-flex justify-content-center">
                        <ul class="pagination mb-0">
                            <li v-for="(link, index) in tenders.links" :key="index" class="page-item"
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

.chat-indicator-blink {
    animation: chat-indicator-blink 1.2s ease-in-out infinite;
}

@keyframes chat-indicator-blink {

    0%,
    100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.6);
    }

    50% {
        box-shadow: 0 0 0 0.45rem rgba(220, 53, 69, 0);
    }
}
</style>
