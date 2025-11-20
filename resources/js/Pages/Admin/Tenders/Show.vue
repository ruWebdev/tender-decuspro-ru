<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    tender: Object,
});

const canRetender = computed(() => {
    if (!props.tender) return false;
    return Boolean(props.tender.is_finished) || props.tender.status === 'closed';
});

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
    const statuses = {
        'open': t('admin.tenders.statuses.open'),
        'closed': t('admin.tenders.statuses.closed'),
        'review': t('admin.tenders.statuses.review'),
    };
    return statuses[status] || status;
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

const retender = () => {
    if (!props.tender) return;
    if (!confirm(t('admin.tenders.actions.confirm_retender', 'Создать переторжку на основе этого тендера?'))) {
        return;
    }

    router.post(route('admin.tenders.retender', props.tender.id));
};
</script>

<template>
    <AdminLayout>
        <div class="admin-tenders-show">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.tenders.show_title') }}</h1>
                <div class="d-flex gap-2">
                    <button v-if="canRetender" type="button" class="btn btn-outline-primary" @click="retender">
                        {{ t('admin.tenders.actions.retender', 'Объявить переторжку') }}
                    </button>
                    <Link :href="route('admin.tenders.edit', tender.id)" class="btn btn-warning">
                    {{ t('admin.tenders.actions.edit') }}
                    </Link>
                    <Link :href="route('admin.tenders.index')" class="btn btn-outline-secondary">
                    {{ t('common.back', 'Назад') }}
                    </Link>
                </div>
            </div>

            <!-- Основная информация -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="m-0">{{ t('common.information', 'Информация') }}</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.form.customer') }}:</strong>
                                {{ tender.customer?.name }} ({{ tender.customer?.email }})
                            </p>
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.form.title') }}:</strong>
                                {{ tender.title }}
                            </p>
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.form.status') }}:</strong>
                                <span class="badge ms-1 me-1 text-light" :class="statusBadgeClass(tender.status)">
                                    {{ statusLabel(tender.status) }}
                                </span>
                                <span class="badge text-light" :class="finishedBadgeClass(tender.is_finished)">
                                    {{ finishedLabel(tender.is_finished) }}
                                </span>
                            </p>
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.form.valid_until') }}:</strong>
                                {{ formatDate(tender.valid_until) }}
                            </p>
                            <p v-if="tender.round_number" class="mb-2">
                                <strong>{{ t('admin.tenders.round_label', 'Раунд торгов') }}:</strong>
                                {{ tender.round_number }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.table.col_created_at') }}:</strong>
                                {{ formatDate(tender.created_at) }}
                            </p>
                            <p v-if="tender.finished_at" class="mb-2">
                                <strong>{{ t('tenders.field_finished_at', 'Завершен') }}:</strong>
                                {{ formatDate(tender.finished_at) }}
                            </p>
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.table.col_items') }}:</strong>
                                {{ tender.items?.length || 0 }}
                            </p>
                        </div>
                    </div>

                    <div v-if="tender.description" class="mt-3">
                        <strong>{{ t('admin.tenders.form.description') }}:</strong>
                        <p class="mt-1">{{ tender.description }}</p>
                    </div>

                    <div v-if="tender.hidden_comment" class="mt-3">
                        <strong>{{ t('admin.tenders.form.hidden_comment') }}:</strong>
                        <p class="mt-1 text-muted">{{ tender.hidden_comment }}</p>
                    </div>
                </div>
            </div>

            <!-- Позиции тендера -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="m-0">{{ t('tenders.positions_title', 'Позиции тендера') }}</h2>
                </div>
                <div class="table-responsive">
                    <div v-if="!tender.items || tender.items.length === 0" class="text-center text-muted py-4">
                        {{ t('admin.tenders.no_items', 'Нет позиций') }}
                    </div>
                    <table v-else class="table card-table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ t('tenders.col_item_title') }}</th>
                                <th>{{ t('tenders.col_quantity') }}</th>
                                <th>{{ t('tenders.col_unit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in tender.items" :key="item.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.title }}</td>
                                <td>{{ item.quantity }}</td>
                                <td>{{ item.unit || '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <!-- Кнопки действий -->
            <div class="card-footer text-end">
                <Link :href="route('admin.tenders.index')" class="btn btn-outline-secondary me-2">
                {{ t('common.back', 'Назад') }}
                </Link>
                <Link :href="route('admin.tenders.edit', tender.id)" class="btn btn-primary">
                {{ t('admin.tenders.actions.edit') }}
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
