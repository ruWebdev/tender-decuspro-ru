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
const search = ref(props.filters.search || '');

const applyFilters = () => {
    router.get(route('admin.platform_suppliers.index'), {
        search: search.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    search.value = '';
    applyFilters();
};

const hasSuppliers = computed(() => suppliers.value && suppliers.value.data && suppliers.value.data.length > 0);

// Импорт из CSV
const showImportModal = ref(false);
const importForm = ref({
    file: null,
    language: 'ru',
});
const importErrors = ref({});
const isImporting = ref(false);

const languages = [
    { value: 'ru', label: 'Русский' },
    { value: 'en', label: 'English' },
    { value: 'cn', label: '中文' },
];

const openImportModal = () => {
    importForm.value = {
        file: null,
        language: 'ru',
    };
    importErrors.value = {};
    showImportModal.value = true;
};

const closeImportModal = () => {
    showImportModal.value = false;
};

const handleFileChange = (event) => {
    const files = event.target.files;
    importForm.value.file = files && files.length ? files[0] : null;
};

const submitImport = () => {
    if (!importForm.value.file) {
        importErrors.value = { file: t('admin.platform_suppliers.import.validation.file_required') };

        return;
    }

    isImporting.value = true;
    importErrors.value = {};

    const formData = new FormData();
    formData.append('file', importForm.value.file);
    formData.append('language', importForm.value.language);

    router.post(route('admin.platform_suppliers.import_csv'), formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            isImporting.value = false;
            closeImportModal();
        },
        onError: (errors) => {
            importErrors.value = errors;
            isImporting.value = false;
        },
    });
};

const totalCount = computed(() => suppliers.value?.total || 0);

const languageLabel = (code) => {
    if (!code) {
        return '-';
    }

    if (code === 'ru') {
        return t('admin.platform_suppliers.languages.ru');
    }

    if (code === 'en') {
        return t('admin.platform_suppliers.languages.en');
    }

    if (code === 'cn') {
        return t('admin.platform_suppliers.languages.cn');
    }

    return code;
};

const sendInvitation = (supplier) => {
    if (!supplier.email) {
        alert(t('admin.platform_suppliers.invitation.no_email'));

        return;
    }

    router.post(route('admin.platform_suppliers.invite', supplier.id), {}, {
        preserveScroll: true,
    });
};

const deleteSupplier = (supplier) => {
    if (!confirm(t('admin.platform_suppliers.actions.confirm_delete'))) {
        return;
    }

    router.delete(route('admin.platform_suppliers.destroy', supplier.id));
};
</script>

<template>
    <AdminLayout>
        <div class="admin-platform-suppliers">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.platform_suppliers.index_title') }}</h1>
                <div class="btn-list">
                    <button type="button" class="btn btn-outline-secondary" @click="openImportModal">
                        {{ t('admin.platform_suppliers.actions.import_csv') }}
                    </button>
                    <Link :href="route('admin.platform_suppliers.create')" class="btn btn-primary">
                    {{ t('admin.platform_suppliers.actions.create') }}
                    </Link>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">{{ t('admin.platform_suppliers.filters.search') }}</label>
                            <input v-model="search" type="text" class="form-control"
                                :placeholder="t('admin.platform_suppliers.filters.search_placeholder')"
                                @keyup.enter="applyFilters">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary me-2" @click="applyFilters">
                                {{ t('common.apply', 'Применить') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary" @click="resetFilters">
                                {{ t('common.reset', 'Сбросить') }}
                            </button>
                        </div>
                        <div class="col-md-5 text-end">
                            <span class="text-muted">{{ t('admin.platform_suppliers.total_count', 'Всего записей') }}:
                                {{ totalCount }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter mb-0">
                        <thead>
                            <tr>
                                <th>{{ t('admin.platform_suppliers.table.col_name') }}</th>
                                <th>{{ t('admin.platform_suppliers.table.col_phone') }}</th>
                                <th>{{ t('admin.platform_suppliers.table.col_email') }}</th>
                                <th>{{ t('admin.platform_suppliers.table.col_website') }}</th>
                                <th>{{ t('admin.platform_suppliers.table.col_comment') }}</th>
                                <th>{{ t('admin.platform_suppliers.table.col_language') }}</th>
                                <th>{{ t('admin.platform_suppliers.table.col_invitation_sent') }}</th>
                                <th class="w-150 text-end">{{ t('admin.platform_suppliers.table.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!hasSuppliers">
                                <td colspan="8" class="text-center text-muted py-4">
                                    {{ t('admin.platform_suppliers.index_empty') }}
                                </td>
                            </tr>
                            <tr v-for="supplier in suppliers.data" :key="supplier.id">
                                <td>{{ supplier.name }}</td>
                                <td>{{ supplier.phone || '-' }}</td>
                                <td>
                                    <a v-if="supplier.email" :href="'mailto:' + supplier.email">
                                        {{ supplier.email }}
                                    </a>
                                    <span v-else>-</span>
                                </td>
                                <td class="w-25">
                                    <a v-if="supplier.website" :href="supplier.website" :title="supplier.website"
                                        target="_blank" rel="noopener noreferrer" class="text-truncate d-inline-block"
                                        style="max-width: 220px;">
                                        {{ supplier.website }}
                                    </a>
                                    <span v-else>-</span>
                                </td>
                                <td>
                                    <span v-if="supplier.comment" class="text-truncate d-inline-block"
                                        style="max-width: 220px;">
                                        {{ supplier.comment }}
                                    </span>
                                    <span v-else class="text-muted">-</span>
                                </td>
                                <td>{{ languageLabel(supplier.language) }}</td>
                                <td class="text-nowrap">
                                    <span v-if="supplier.invitation_sent" class="badge bg-success">
                                        {{ t('admin.platform_suppliers.badges.yes') }}
                                    </span>
                                    <span v-else class="badge bg-secondary text-light">
                                        {{ t('admin.platform_suppliers.badges.no') }}
                                    </span>
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2"
                                        @click="sendInvitation(supplier)">
                                        {{ t('admin.platform_suppliers.invitation.send') }}
                                    </button>
                                </td>
                                <td class="text-end">
                                    <div class="btn-list flex-nowrap justify-content-end">
                                        <Link :href="route('admin.platform_suppliers.edit', supplier.id)"
                                            class="btn btn-sm btn-ghost-primary">
                                        {{ t('common.edit', 'Редактировать') }}
                                        </Link>
                                        <button type="button" class="btn btn-sm btn-ghost-danger"
                                            @click="deleteSupplier(supplier)">
                                            {{ t('common.delete', 'Удалить') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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
        <!-- Модальное окно импорта CSV -->
        <div v-if="showImportModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ t('admin.platform_suppliers.import.title') }}</h5>
                        <button type="button" class="btn-close" @click="closeImportModal"></button>
                    </div>
                    <form @submit.prevent="submitImport">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.import.field_file') }}
                                    *</label>
                                <input type="file" class="form-control" accept=".json,application/json,.csv,text/csv"
                                    :class="{ 'is-invalid': importErrors.file }" @change="handleFileChange">
                                <div v-if="importErrors.file" class="invalid-feedback">{{ importErrors.file }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.import.field_language') }}
                                    *</label>
                                <select v-model="importForm.language" class="form-select"
                                    :class="{ 'is-invalid': importErrors.language }">
                                    <option v-for="lang in languages" :key="lang.value" :value="lang.value">
                                        {{ lang.label }}
                                    </option>
                                </select>
                                <div v-if="importErrors.language" class="invalid-feedback">
                                    {{ importErrors.language }}
                                </div>
                            </div>

                            <div class="alert alert-info mb-0">
                                {{ t('admin.platform_suppliers.import.hint') }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeImportModal">
                                {{ t('common.cancel', 'Отмена') }}
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="isImporting">
                                {{ t('admin.platform_suppliers.import.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.w-150 {
    width: 150px;
}
</style>
