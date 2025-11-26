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

const totalCount = computed(() => suppliers.value?.total || 0);
</script>

<template>
    <AdminLayout>
        <div class="admin-platform-suppliers">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.platform_suppliers.index_title') }}</h1>
                <Link :href="route('admin.platform_suppliers.create')" class="btn btn-primary">
                {{ t('admin.platform_suppliers.actions.create') }}
                </Link>
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
                            <span class="text-muted">{{ t('admin.platform_suppliers.total_count', 'Всего записей') }}: {{ totalCount }}</span>
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
                                <th class="w-150 text-end">{{ t('admin.platform_suppliers.table.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!hasSuppliers">
                                <td colspan="6" class="text-center text-muted py-4">
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
                                <td>
                                    <a v-if="supplier.website" :href="supplier.website" target="_blank" rel="noopener noreferrer">
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
                                <td class="text-end">
                                    <div class="btn-list flex-nowrap justify-content-end">
                                        <Link :href="route('admin.platform_suppliers.edit', supplier.id)"
                                            class="btn btn-sm btn-ghost-primary">
                                        {{ t('common.edit', 'Редактировать') }}
                                        </Link>
                                        <button type="button" class="btn btn-sm btn-ghost-danger"
                                            @click="() => { if (confirm(t('admin.platform_suppliers.actions.confirm_delete'))) { router.delete(route('admin.platform_suppliers.destroy', supplier.id)); } }">
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
    </AdminLayout>
</template>

<style scoped>
.w-150 {
    width: 150px;
}
</style>
