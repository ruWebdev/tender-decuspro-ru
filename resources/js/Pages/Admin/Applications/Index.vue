<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

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
</script>

<template>
    <AdminLayout>
        <div class="admin-applications-index">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.applications.index_title', 'Заявки на тендеры') }}</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <div v-if="proposals.data.length === 0" class="text-muted">
                        {{ t('admin.applications.empty', 'Заявки пока отсутствуют.') }}
                    </div>

                    <div v-else class="table-responsive">
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
                                    <td>{{ proposal.status }}</td>
                                    <td>{{ proposal.created_at }}</td>
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
                            <dd class="col-8">{{ selectedProposal.status }}</dd>

                            <dt class="col-4">{{ t('admin.applications.table.col_created_at', 'Дата заявки') }}</dt>
                            <dd class="col-8">{{ selectedProposal.created_at }}</dd>
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
