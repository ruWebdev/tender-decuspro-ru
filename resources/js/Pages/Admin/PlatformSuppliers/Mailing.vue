<script setup>
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    mailings: Object,
    templates: Array,
    tenders: Array,
    total_suppliers: Number,
});

const mailings = computed(() => props.mailings || { data: [] });
const hasMailings = computed(() => mailings.value?.data?.length > 0);

// Модальное окно создания рассылки
const showCreateModal = ref(false);
const form = ref({
    name: '',
    emails_limit: 50,
    notification_template_id: '',
    tender_ids: [],
    company_filter: '',
    language: 'ru',
});
const formErrors = ref({});
const isSubmitting = ref(false);

const languages = [
    { value: 'ru', label: 'Русский' },
    { value: 'en', label: 'English' },
    { value: 'cn', label: '中文' },
];

const openCreateModal = () => {
    form.value = {
        name: '',
        emails_limit: 50,
        notification_template_id: '',
        tender_ids: [],
        company_filter: '',
        language: 'ru',
    };
    formErrors.value = {};
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
};

const submitForm = () => {
    isSubmitting.value = true;
    formErrors.value = {};

    router.post(route('admin.platform_suppliers.mailing.store'), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            closeCreateModal();
            isSubmitting.value = false;
        },
        onError: (errors) => {
            formErrors.value = errors;
            isSubmitting.value = false;
        },
    });
};

const startMailing = (mailing) => {
    router.post(route('admin.platform_suppliers.mailing.start', mailing.id), {}, {
        preserveScroll: true,
    });
};

const stopMailing = (mailing) => {
    router.post(route('admin.platform_suppliers.mailing.stop', mailing.id), {}, {
        preserveScroll: true,
    });
};

const deleteMailing = (mailing) => {
    if (!confirm(t('admin.platform_suppliers.mailing.confirm_delete'))) {
        return;
    }

    router.delete(route('admin.platform_suppliers.mailing.destroy', mailing.id), {
        preserveScroll: true,
    });
};

const statusLabel = (status) => {
    const labels = {
        draft: t('admin.platform_suppliers.mailing.status.draft'),
        running: t('admin.platform_suppliers.mailing.status.running'),
        paused: t('admin.platform_suppliers.mailing.status.paused'),
        completed: t('admin.platform_suppliers.mailing.status.completed'),
    };

    return labels[status] || status;
};

const statusClass = (status) => {
    const classes = {
        draft: 'bg-secondary',
        running: 'bg-primary',
        paused: 'bg-warning',
        completed: 'bg-success',
    };

    return classes[status] || 'bg-secondary';
};

const progressPercent = (mailing) => {
    const limit = mailing.emails_limit && mailing.emails_limit > 0
        ? mailing.emails_limit
        : mailing.total_recipients;

    if (!limit || limit === 0) {
        return 0;
    }

    return Math.round((mailing.sent_count / limit) * 100);
};

const canStart = (mailing) => {
    return mailing.status === 'draft' || mailing.status === 'paused';
};

const canStop = (mailing) => {
    return mailing.status === 'running';
};

const toggleTender = (tenderId) => {
    const index = form.value.tender_ids.indexOf(tenderId);

    if (index === -1) {
        form.value.tender_ids.push(tenderId);
    } else {
        form.value.tender_ids.splice(index, 1);
    }
};

const isTenderSelected = (tenderId) => {
    return form.value.tender_ids.includes(tenderId);
};
</script>

<template>
    <AdminLayout>
        <div class="admin-platform-suppliers-mailing">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    {{ t('admin.platform_suppliers.mailing_title') }}
                </h1>
                <button type="button" class="btn btn-primary" @click="openCreateModal">
                    {{ t('admin.platform_suppliers.mailing.create_btn') }}
                </button>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ t('admin.platform_suppliers.mailing.results_title') }}</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter mb-0">
                        <thead>
                            <tr>
                                <th>{{ t('admin.platform_suppliers.mailing.col_name') }}</th>
                                <th>{{ t('admin.platform_suppliers.mailing.col_type') }}</th>
                                <th>{{ t('admin.platform_suppliers.mailing.col_progress') }}</th>
                                <th class="text-end">{{ t('admin.platform_suppliers.mailing.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!hasMailings">
                                <td colspan="4" class="text-center text-muted py-4">
                                    {{ t('admin.platform_suppliers.mailing.empty') }}
                                </td>
                            </tr>
                            <tr v-for="mailing in mailings.data" :key="mailing.id">
                                <td>
                                    <div class="fw-bold">{{ mailing.name }}</div>
                                    <small class="text-muted">
                                        <span class="badge text-light" :class="statusClass(mailing.status)">
                                            {{ statusLabel(mailing.status) }}
                                        </span>
                                    </small>
                                </td>
                                <td>
                                    {{ mailing.notification_template?.name || '-' }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px; min-width: 100px;">
                                            <div class="progress-bar" role="progressbar"
                                                :style="{ width: progressPercent(mailing) + '%' }"
                                                :aria-valuenow="progressPercent(mailing)" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="text-muted small">
                                            {{ mailing.sent_count }} /
                                            {{ mailing.emails_limit && mailing.emails_limit > 0
                                                ? mailing.emails_limit
                                                : mailing.total_recipients }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-list flex-nowrap justify-content-end">
                                        <button v-if="canStart(mailing)" type="button"
                                            class="btn btn-sm btn-outline-success" @click="startMailing(mailing)">
                                            {{ t('admin.platform_suppliers.mailing.btn_start') }}
                                        </button>
                                        <button v-if="canStop(mailing)" type="button"
                                            class="btn btn-sm btn-outline-warning" @click="stopMailing(mailing)">
                                            {{ t('admin.platform_suppliers.mailing.btn_stop') }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            @click="deleteMailing(mailing)">
                                            {{ t('admin.platform_suppliers.mailing.btn_delete') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="mailings.links && mailings.links.length > 3" class="card-footer">
                    <nav class="d-flex justify-content-center">
                        <ul class="pagination mb-0">
                            <li v-for="(link, index) in mailings.links" :key="index" class="page-item"
                                :class="{ active: link.active, disabled: !link.url }">
                                <a v-if="link.url" :href="link.url" class="page-link" v-html="link.label" />
                                <span v-else class="page-link" v-html="link.label" />
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Модальное окно создания рассылки -->
        <div v-if="showCreateModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ t('admin.platform_suppliers.mailing.modal_title') }}</h5>
                        <button type="button" class="btn-close" @click="closeCreateModal"></button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.mailing.field_name') }}
                                    *</label>
                                <input v-model="form.name" type="text" class="form-control"
                                    :class="{ 'is-invalid': formErrors.name }">
                                <div v-if="formErrors.name" class="invalid-feedback">{{ formErrors.name }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.mailing.field_emails_limit') }}
                                    *</label>
                                <input v-model.number="form.emails_limit" type="number" min="1" class="form-control"
                                    :class="{ 'is-invalid': formErrors.emails_limit }">
                                <div v-if="formErrors.emails_limit" class="invalid-feedback">{{ formErrors.emails_limit
                                }}</div>
                                <small class="text-muted">{{ t('admin.platform_suppliers.mailing.emails_limit_hint')
                                }}</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.mailing.field_template') }}
                                    *</label>
                                <select v-model="form.notification_template_id" class="form-select"
                                    :class="{ 'is-invalid': formErrors.notification_template_id }">
                                    <option value="">{{ t('admin.platform_suppliers.mailing.select_template') }}
                                    </option>
                                    <option v-for="template in templates" :key="template.id" :value="template.id">
                                        {{ template.name }}
                                    </option>
                                </select>
                                <div v-if="formErrors.notification_template_id" class="invalid-feedback">
                                    {{ formErrors.notification_template_id }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.mailing.field_language') }}
                                    *</label>
                                <select v-model="form.language" class="form-select"
                                    :class="{ 'is-invalid': formErrors.language }">
                                    <option v-for="lang in languages" :key="lang.value" :value="lang.value">
                                        {{ lang.label }}
                                    </option>
                                </select>
                                <div v-if="formErrors.language" class="invalid-feedback">
                                    {{ formErrors.language }}
                                </div>
                                <small class="text-muted">{{ t('admin.platform_suppliers.mailing.language_hint')
                                }}</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.mailing.field_tenders')
                                }}</label>
                                <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                    <div v-if="!tenders || tenders.length === 0" class="text-muted">
                                        {{ t('admin.platform_suppliers.mailing.no_tenders') }}
                                    </div>
                                    <div v-for="tender in tenders" :key="tender.id" class="form-check">
                                        <input type="checkbox" class="form-check-input" :id="'tender-' + tender.id"
                                            :checked="isTenderSelected(tender.id)" @change="toggleTender(tender.id)">
                                        <label class="form-check-label" :for="'tender-' + tender.id">
                                            {{ tender.title }}
                                        </label>
                                    </div>
                                </div>
                                <small class="text-muted">{{ t('admin.platform_suppliers.mailing.tenders_hint')
                                }}</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.mailing.field_company_filter')
                                }}</label>
                                <input v-model="form.company_filter" type="text" class="form-control"
                                    :class="{ 'is-invalid': formErrors.company_filter }"
                                    :placeholder="t('admin.platform_suppliers.mailing.company_filter_placeholder')">
                                <div v-if="formErrors.company_filter" class="invalid-feedback">{{
                                    formErrors.company_filter }}</div>
                                <small class="text-muted">{{ t('admin.platform_suppliers.mailing.company_filter_hint')
                                }}</small>
                            </div>

                            <div class="alert alert-info mb-0">
                                {{ t('admin.platform_suppliers.mailing.total_suppliers_hint') }}: <strong>{{
                                    total_suppliers }}</strong>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeCreateModal">
                                {{ t('common.cancel', 'Отмена') }}
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                                {{ t('admin.platform_suppliers.mailing.btn_create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.progress {
    background-color: #e9ecef;
}
</style>
