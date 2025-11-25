<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    user: Object,
    profile: Object,
    documents: Array,
    recent_proposals: Array,
    won_tenders: Array,
});

const user = computed(() => props.user || {});
const profile = computed(() => props.profile || null);
const documents = computed(() => props.documents || []);
const recentProposals = computed(() => props.recent_proposals || []);
const wonTenders = computed(() => props.won_tenders || []);

const isBlocked = computed(() => user.value.is_blocked);

const blockUser = () => {
    if (confirm(t('admin.users.actions.confirm_block'))) {
        router.post(route('admin.users.block', user.value.id));
    }
};

const unblockUser = () => {
    router.post(route('admin.users.unblock', user.value.id));
};

const formatDateTime = (value) => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    return date.toLocaleString(page.props.locale || 'ru');
};

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    return date.toLocaleDateString(page.props.locale || 'ru');
};

const documentLabel = (type) => {
    const map = {
        business_license: 'common.doc_business_license',
        tax_certificate: 'common.doc_tax_certificate',
        power_of_attorney: 'common.doc_power_of_attorney',
        board_resolution: 'common.doc_board_resolution',
        passport_director: 'common.doc_passport_director',
        passport_signatory: 'common.doc_passport_signatory',
    };

    const key = map[type] || null;
    return key ? t(key) : type;
};

const docStatusLabel = (status) => {
    if (status === 'pending') {
        return t('common.doc_status_pending', 'На проверке');
    }
    if (status === 'approved') {
        return t('common.doc_status_approved', 'Проверено');
    }
    if (status === 'rejected') {
        return t('common.doc_status_rejected', 'Отклонено');
    }
    return t('common.unknown');
};

const docStatusBadgeClass = (status) => {
    if (status === 'pending') {
        return 'badge bg-info text-light';
    }
    if (status === 'approved') {
        return 'badge bg-success text-light';
    }
    if (status === 'rejected') {
        return 'badge bg-danger text-light';
    }
    return 'badge bg-secondary text-light';
};

const approveDocument = (doc) => {
    router.post(route('admin.suppliers.documents.approve', { document: doc.id }));
};

const rejectDocument = (doc) => {
    const comment = window.prompt(t('admin.suppliers.actions.reject_comment_prompt', 'Укажите причину отказа в модерации:'), '');
    if (comment === null) {
        return;
    }

    router.post(route('admin.suppliers.documents.reject', { document: doc.id }), {
        moderation_comment: comment,
    });
};
</script>

<template>
    <AdminLayout>
        <div class="admin-supplier-show">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">
                        {{ t('admin.suppliers.card_title', 'Карточка поставщика') }}
                    </h1>
                    <p class="text-muted mb-0">
                        {{ user.name }} · {{ user.email }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <Link :href="route('admin.suppliers.index')" class="btn btn-outline-secondary">
                    {{ t('common.back', 'Назад') }}
                    </Link>
                    <Link :href="route('admin.users.edit', user.id)" class="btn btn-outline-primary">
                    {{ t('admin.users.actions.edit') }}
                    </Link>
                    <button v-if="!isBlocked" type="button" class="btn btn-outline-danger" @click="blockUser">
                        {{ t('admin.users.actions.block') }}
                    </button>
                    <button v-else type="button" class="btn btn-outline-success" @click="unblockUser">
                        {{ t('admin.users.actions.unblock') }}
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                {{ t('common.profile_company', 'Профиль компании') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <template v-if="profile">
                                <p>
                                    <strong>{{ t('common.company_name') }}:</strong>
                                    {{ profile.company_name }}
                                </p>
                                <p v-if="profile.contact_data?.phone">
                                    <strong>{{ t('common.phone') }}:</strong>
                                    {{ profile.contact_data.phone }}
                                </p>
                                <p v-if="profile.contact_data?.address">
                                    <strong>{{ t('common.address') }}:</strong>
                                    {{ profile.contact_data.address }}
                                </p>
                                <p v-if="profile.contact_data?.website">
                                    <strong>{{ t('common.website') }}:</strong>
                                    {{ profile.contact_data.website }}
                                </p>
                            </template>
                            <p v-else class="text-muted mb-0">
                                {{ t('admin.suppliers.profile_empty', 'Профиль компании пока не заполнен.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                {{ t('admin.suppliers.documents_title', 'Документы поставщика') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div v-if="documents.length === 0" class="text-muted">
                                {{ t('admin.suppliers.documents_empty', 'Документы пока не загружены.') }}
                            </div>
                            <div v-else class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ t('admin.suppliers.table.col_document', 'Документ') }}</th>
                                            <th>{{ t('common.status', 'Статус') }}</th>
                                            <th>{{ t('common.info', 'Информация') }}</th>
                                            <th class="text-end">
                                                {{ t('admin.users.table.col_actions', 'Действия') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="doc in documents" :key="doc.id">
                                            <td>
                                                {{ documentLabel(doc.type) }}
                                                <div class="small text-muted">
                                                    {{ formatDate(doc.created_at) }}
                                                </div>
                                            </td>
                                            <td>
                                                <span :class="docStatusBadgeClass(doc.status)">
                                                    {{ docStatusLabel(doc.status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <div v-if="doc.file_path">
                                                        <a :href="`/storage/${doc.file_path}`" target="_blank"
                                                            rel="noopener">
                                                            {{ t('common.current_file') }}
                                                        </a>
                                                    </div>
                                                    <div v-if="doc.moderation_comment" class="text-muted mt-1">
                                                        {{ doc.moderation_comment }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-list flex-nowrap justify-content-end">
                                                    <button type="button" class="btn btn-sm btn-ghost-success"
                                                        @click="approveDocument(doc)">
                                                        {{ t('admin.suppliers.actions.approve', 'Одобрить') }}
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-ghost-danger"
                                                        @click="rejectDocument(doc)">
                                                        {{ t('admin.suppliers.actions.reject', 'Отклонить') }}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                {{ t('admin.suppliers.activity.recent_proposals', 'Последние отклики на тендеры') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div v-if="recentProposals.length === 0" class="text-muted">
                                {{ t('admin.suppliers.activity.no_proposals', 'Отклики пока отсутствуют.') }}
                            </div>
                            <div v-else class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ t('admin.suppliers.activity.col_tender', 'Тендер') }}</th>
                                            <th>{{ t('admin.suppliers.activity.col_status', 'Статус') }}</th>
                                            <th>{{ t('admin.suppliers.activity.col_submitted_at', 'Дата отклика') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="p in recentProposals" :key="p.id">
                                            <td>
                                                <Link :href="route('admin.tenders.show', p.tender_id)">
                                                {{ p.tender?.title || '#' + p.tender_id }}
                                                </Link>
                                            </td>
                                            <td>{{ p.status }}</td>
                                            <td>{{ formatDateTime(p.submitted_at) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                {{ t('admin.suppliers.activity.wins', 'Выигранные тендеры') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div v-if="wonTenders.length === 0" class="text-muted">
                                {{ t('admin.suppliers.activity.no_wins', 'Выигранные тендеры пока отсутствуют.') }}
                            </div>
                            <ul v-else class="list-group list-group-flush">
                                <li v-for="tender in wonTenders" :key="tender.id"
                                    class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <Link :href="route('admin.tenders.show', tender.id)">
                                        {{ tender.title }}
                                        </Link>
                                        <div class="small text-muted">
                                            {{ formatDate(tender.finished_at) }}
                                        </div>
                                    </div>
                                    <span class="badge bg-success text-light">
                                        {{ t('admin.tenders.statuses.closed', 'Закрыт') }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.btn-list {
    display: flex;
    gap: 0.25rem;
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
