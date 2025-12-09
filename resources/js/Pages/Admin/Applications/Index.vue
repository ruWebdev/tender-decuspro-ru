<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const page = usePage();

const currentLocale = computed(() => page.props.locale || 'ru');

const jsLocale = computed(() => {
    if (currentLocale.value === 'en') {
        return 'en-US';
    }

    if (currentLocale.value === 'cn') {
        return 'zh-CN';
    }

    return 'ru-RU';
});

const formatDateTime = (value) => {
    if (!value) {
        return '-';
    }

    const d = new Date(value);

    return d.toLocaleString(jsLocale.value, {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const props = defineProps({
    proposals: Object,
    filters: Object,
});

const selectedProposal = ref(null);
const isProcessing = ref(false);

const openProposal = (proposal) => {
    selectedProposal.value = proposal;
};

const closeOffcanvas = () => {
    if (isProcessing.value) return;
    selectedProposal.value = null;
};

const approveProposal = () => {
    if (!selectedProposal.value) return;
    isProcessing.value = true;
    router.post(route('admin.applications.approve', selectedProposal.value.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            isProcessing.value = false;
        },
    });
};

const rejectProposal = () => {
    if (!selectedProposal.value) return;
    isProcessing.value = true;
    router.post(route('admin.applications.reject', selectedProposal.value.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            isProcessing.value = false;
        },
    });
};

const formatNumber = (value) => {
    const num = Number(value);

    if (!Number.isFinite(num)) {
        return '-';
    }

    return num.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const getStatusLabel = (status) => {
    const s = status;

    if (s === 'submitted') {
        return t('proposals.status_submitted', 'Отправлено');
    }

    if (s === 'draft') {
        return t('proposals.status_draft', 'Черновик');
    }

    if (s === 'withdrawn') {
        return t('proposals.status_withdrawn', 'Отозвано');
    }

    if (s === 'approved') {
        return t('proposals.status_approved', 'Принято');
    }

    if (s === 'rejected') {
        return t('proposals.status_rejected', 'Отклонено');
    }

    return s || '-';
};

const getStatusBadgeClass = (status) => {
    const s = status;

    if (s === 'submitted') {
        return 'badge bg-info text-light';
    }

    if (s === 'draft') {
        return 'badge bg-secondary text-light';
    }

    if (s === 'withdrawn') {
        return 'badge bg-dark text-light';
    }

    if (s === 'approved') {
        return 'badge bg-success text-light';
    }

    if (s === 'rejected') {
        return 'badge bg-danger text-light';
    }

    return 'badge bg-secondary text-light';
};
</script>

<template>
    <AdminLayout>
        <div class="admin-applications-index">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.applications.index_title', 'Заявки на тендеры') }}</h1>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div v-if="proposals.data.length === 0" class="text-muted">
                        {{ t('admin.applications.empty', 'Заявки пока отсутствуют.') }}
                    </div>

                    <div v-else>
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ t('admin.applications.table.col_tender', 'Тендер') }}</th>
                                    <th>{{ t('admin.applications.table.col_supplier', 'Поставщик') }}</th>
                                    <th>{{ t('admin.applications.table.col_status', 'Статус') }}</th>
                                    <th>{{ t('admin.applications.table.col_created_at', 'Дата заявки') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="proposal in proposals.data" :key="proposal.id"
                                    @click="openProposal(proposal)" style="cursor: pointer;">
                                    <td>{{ proposal.id }}</td>
                                    <td>{{ proposal.tender?.title }}</td>
                                    <td>{{ proposal.user?.name }}</td>
                                    <td>
                                        <span :class="getStatusBadgeClass(proposal.status)">{{
                                            getStatusLabel(proposal.status) }}</span>
                                    </td>
                                    <td>{{ formatDateTime(proposal.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Offcanvas с деталями заявки -->
            <div v-if="selectedProposal" class="offcanvas-backdrop-custom" @click="closeOffcanvas">
                <div class="offcanvas-panel" @click.stop>
                    <div class="offcanvas-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ t('admin.applications.offcanvas.title', 'Детали заявки') }}</h5>
                        <button type="button" class="btn-close" aria-label="Close" @click="closeOffcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <h6 class="mb-2">{{ t('admin.applications.offcanvas.proposal_info', 'Информация о заявке') }}
                        </h6>
                        <dl class="row small">
                            <dt class="col-4">ID</dt>
                            <dd class="col-8">{{ selectedProposal.id }}</dd>

                            <dt class="col-4">{{ t('admin.applications.table.col_tender', 'Тендер') }}</dt>
                            <dd class="col-8">{{ selectedProposal.tender?.title }}</dd>

                            <dt class="col-4">{{ t('admin.applications.table.col_status', 'Статус') }}</dt>
                            <dd class="col-8">
                                <span :class="getStatusBadgeClass(selectedProposal.status)">{{
                                    getStatusLabel(selectedProposal.status) }}</span>
                            </dd>

                            <dt class="col-4">{{ t('admin.applications.table.col_created_at', 'Дата заявки') }}</dt>
                            <dd class="col-8">{{ formatDateTime(selectedProposal.created_at) }}</dd>
                        </dl>

                        <hr />

                        <h6 class="mb-2">{{ t('admin.applications.offcanvas.supplier_info', 'Информация о поставщике')
                        }}</h6>
                        <dl class="row small">
                            <dt class="col-4">{{ t('common.name', 'Имя') }}</dt>
                            <dd class="col-8">{{ selectedProposal.user?.name }}</dd>

                            <dt class="col-4">Email</dt>
                            <dd class="col-8">{{ selectedProposal.user?.email }}</dd>

                            <dt class="col-4">{{ t('common.company_name', 'Название компании') }}</dt>
                            <dd class="col-8">{{ selectedProposal.user?.supplier_profile?.company_name }}</dd>

                            <dt class="col-4">{{ t('common.phone', 'Телефон') }}</dt>
                            <dd class="col-8">{{ selectedProposal.user?.supplier_profile?.contact_data?.phone }}</dd>
                        </dl>

                        <hr />

                        <h6 class="mb-2">
                            {{ t('admin.applications.offcanvas.positions_title', 'Позиции в заявке') }}
                        </h6>

                        <div v-if="selectedProposal.items && selectedProposal.items.length" class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ t('proposals.col_item_title', 'Позиция') }}</th>
                                        <th>{{ t('proposals.col_quantity', 'Кол-во') }}</th>
                                        <th>{{ t('proposals.col_unit', 'Ед. изм.') }}</th>
                                        <th>{{ t('proposals.col_price', 'Цена') }}</th>
                                        <th>{{ t('proposals.col_comment', 'Комм.') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="small" v-for="item in selectedProposal.items" :key="item.id">
                                        <td>{{ item.tender_item?.title || t('proposals.fallback_item', 'Позиция') }}
                                        </td>
                                        <td>{{ item.tender_item?.quantity }}</td>
                                        <td>{{ item.tender_item?.unit }}</td>
                                        <td>{{ formatNumber(item.price) }}</td>
                                        <td>{{ item.comment }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-muted small">
                            {{ t('admin.applications.offcanvas.positions_empty', 'Позиции для этой заявки не найдены.')
                            }}
                        </div>
                    </div>

                    <div class="offcanvas-footer d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" @click="closeOffcanvas"
                            :disabled="isProcessing">
                            {{ t('common.cancel', 'Отмена') }}
                        </button>
                        <button type="button" class="btn btn-danger" @click="rejectProposal" :disabled="isProcessing">
                            {{ t('admin.suppliers.actions.reject', 'Отклонить') }}
                        </button>
                        <button type="button" class="btn btn-success" @click="approveProposal" :disabled="isProcessing">
                            {{ t('admin.suppliers.actions.approve', 'Принять') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.offcanvas-backdrop-custom {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: flex-end;
    z-index: 1050;
}

.offcanvas-panel {
    width: 50%;
    max-width: 720px;
    background: #fff;
    display: flex;
    flex-direction: column;
}

.offcanvas-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e5e7eb;
}

.offcanvas-body {
    padding: 1rem 1.25rem;
    overflow-y: auto;
    max-height: calc(100vh - 7rem);
}

.offcanvas-footer {
    padding: 0.75rem 1.25rem;
    border-top: 1px solid #e5e7eb;
    background-color: #f9fafb;
}
</style>
