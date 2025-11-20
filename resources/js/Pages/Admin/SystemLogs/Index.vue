<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    logs: Array,
    filters: Object,
    levels: Array,
});

const search = ref({
    level: props.filters.level || '',
    code: props.filters.code || '',
    from: props.filters.from || '',
    to: props.filters.to || '',
});

const applyFilters = () => {
    router.get(route('admin.system_logs.index'), search.value, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    search.value = { level: '', code: '', from: '', to: '' };
    applyFilters();
};

const formatDateTime = (value) => {
    if (!value) return '-';
    const d = new Date(value);
    return d.toLocaleString(page.props.locale || 'ru');
};

const formatContext = (ctx) => {
    if (!ctx) return '';
    try {
        const json = typeof ctx === 'string' ? JSON.parse(ctx) : ctx;
        const str = JSON.stringify(json, null, 2);
        return str.length > 300 ? str.slice(0, 300) + '…' : str;
    } catch (e) {
        return String(ctx);
    }
};

const levelBadgeClass = (level) => {
    if (level === 'error') return 'bg-danger';
    if (level === 'warning') return 'bg-warning text-dark';
    if (level === 'business') return 'bg-success';
    return 'bg-secondary';
};
</script>

<template>
    <AdminLayout>
        <div class="admin-system-logs">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.system_logs.title') }}</h1>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ t('admin.system_logs.filters.title', 'Фильтры') }}</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ t('admin.system_logs.filters.level', 'Уровень') }}</label>
                            <select v-model="search.level" class="form-select" @change="applyFilters">
                                <option value="">{{ t('admin.system_logs.filters.all', 'Все') }}</option>
                                <option v-for="lvl in levels" :key="lvl" :value="lvl">{{ lvl }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ t('admin.system_logs.filters.code', 'Код события') }}</label>
                            <input v-model="search.code" type="text" class="form-control" @keyup.enter="applyFilters" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ t('admin.system_logs.filters.from', 'С даты') }}</label>
                            <input v-model="search.from" type="date" class="form-control" @change="applyFilters" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ t('admin.system_logs.filters.to', 'По дату') }}</label>
                            <input v-model="search.to" type="date" class="form-control" @change="applyFilters" />
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" @click="applyFilters">
                            {{ t('common.apply', 'Применить') }}
                        </button>
                        <button type="button" class="btn btn-outline-secondary" @click="resetFilters">
                            {{ t('common.reset', 'Сбросить') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div v-if="!logs || logs.length === 0" class="text-center text-muted py-4">
                        {{ t('admin.system_logs.empty', 'Логи пока отсутствуют') }}
                    </div>
                    <div v-else class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>{{ t('admin.system_logs.table.level', 'Уровень') }}</th>
                                    <th>{{ t('admin.system_logs.table.code', 'Код') }}</th>
                                    <th>{{ t('admin.system_logs.table.message', 'Сообщение') }}</th>
                                    <th>{{ t('admin.system_logs.table.context', 'Контекст') }}</th>
                                    <th>{{ t('admin.system_logs.table.created_at', 'Создан') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in logs" :key="log.id">
                                    <td>
                                        <span class="badge text-light" :class="levelBadgeClass(log.level)">{{ log.level
                                            }}</span>
                                    </td>
                                    <td>{{ log.code || '—' }}</td>
                                    <td>{{ log.message }}</td>
                                    <td>
                                        <pre class="mb-0 small text-muted"
                                            v-if="log.context">{{ formatContext(log.context) }}</pre>
                                    </td>
                                    <td>{{ formatDateTime(log.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-system-logs pre {
    max-height: 120px;
    overflow: auto;
    white-space: pre-wrap;
}
</style>
