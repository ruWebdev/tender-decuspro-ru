<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const stats = computed(() => page.props.stats || {});
const recentTenders = computed(() => page.props.recentTenders || []);

const procurementVolume = computed(() => page.props.procurement_volume || 0);
const topSuppliers = computed(() => (page.props.top_suppliers || []).slice(0, 5));

const shortcuts = computed(() => [
    {
        label: t('admin.users.title'),
        href: '/admin/users',
        variant: 'primary',
    },
    {
        label: t('admin.tenders.title'),
        href: '/admin/tenders',
        variant: 'outline-primary',
    },
    {
        label: t('admin.content.title'),
        href: '/admin/content',
        variant: 'outline-success',
    },
    {
        label: t('admin.ai.title'),
        href: '/admin/ai',
        variant: 'outline-info',
    },
]);

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

const tenderStatusLabel = (status) => {
    const key = `admin.tenders.statuses.${status}`;
    const translated = t(key, '');
    return translated !== key ? translated : status;
};

const statusBadgeClass = (status) => {
    if (status === 'open') {
        return 'bg-success';
    }

    if (status === 'closed') {
        return 'bg-secondary';
    }

    if (status === 'review') {
        return 'bg-warning';
    }

    return 'bg-dark';
};

const currencyByLocale = (locale) => {
    if (!locale) return 'RUB';
    const l = String(locale).toLowerCase();
    if (l.startsWith('ru')) return 'RUB';
    if (l.startsWith('en')) return 'USD';
    if (l.startsWith('zh') || l.startsWith('cn')) return 'CNY';
    return 'RUB';
};

const formatNumber = (value) => {
    try {
        return new Intl.NumberFormat(page.props.locale || 'ru').format(value ?? 0);
    } catch (_) {
        return String(value ?? 0);
    }
};

const formatCurrency = (value) => {
    const currency = currencyByLocale(page.props.locale || 'ru');
    try {
        return new Intl.NumberFormat(page.props.locale || 'ru', {
            style: 'currency',
            currency,
            maximumFractionDigits: 2,
        }).format(value ?? 0);
    } catch (_) {
        const symbol = currency === 'RUB' ? '₽' : currency === 'USD' ? '$' : currency === 'CNY' ? '¥' : '';
        return `${formatNumber(value ?? 0)} ${symbol}`.trim();
    }
};
</script>

<template>
    <AdminLayout>
        <div class="admin-dashboard">

            <div class="row g-3 mb-4">
                <div class="col-md-4" v-for="(value, key) in stats" :key="key">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="text-muted text-uppercase small mb-1">{{ t(`admin.stats.${key}`) }}</p>
                            <p class="display-6 fw-semibold mb-0">
                                <span v-if="key === 'budget_savings'">{{ formatCurrency(value) }}</span>
                                <span v-else-if="key === 'average_bids_per_tender'">{{ Number(value ||
                                    0).toLocaleString(page.props.locale || 'ru', { maximumFractionDigits: 1 }) }}</span>
                                <span v-else>{{ formatNumber(value) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="h5 mb-0">{{ t('admin.sections.recent_activity_title', 'Последние тендеры') }}
                            </h3>
                            <Link href="/admin/tenders" class="btn btn-link p-0">{{ t('admin.tenders.title') }}</Link>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ t('admin.tenders.table.col_title') }}</th>
                                        <th>{{ t('admin.tenders.table.col_status') }}</th>
                                        <th>{{ t('admin.tenders.table.col_created_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="recentTenders.length === 0">
                                        <td colspan="3" class="text-center text-muted py-4">{{ t('common.no_data',
                                            'Нет данных') }}</td>
                                    </tr>
                                    <tr v-for="tender in recentTenders" :key="tender.id">
                                        <td>{{ tender.title }}</td>
                                        <td>
                                            <span class="badge text-light" :class="statusBadgeClass(tender.status)">
                                                {{ tenderStatusLabel(tender.status) }}
                                            </span>
                                        </td>
                                        <td>{{ formatDate(tender.created_at) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="h5 mb-0">{{ t('admin.sections.shortcuts_title', 'Быстрые ссылки') }}</h3>
                        </div>
                        <div class="card-body d-flex flex-column gap-2">
                            <Link v-for="shortcut in shortcuts" :key="shortcut.label" :href="shortcut.href"
                                class="btn w-100" :class="`btn-${shortcut.variant}`">
                            {{ shortcut.label }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-4">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="h5 mb-0">{{ t('admin.analytics.procurement_volume_title',
                                'Объем проведенных закупок за период') }}</h3>
                            <span class="text-muted small">{{ t('admin.analytics.current_period', 'за текущий месяц')
                            }}</span>
                        </div>
                        <div class="card-body">
                            <p class="display-6 fw-semibold mb-0">{{ formatCurrency(procurementVolume) }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="h5 mb-0">{{ t('admin.analytics.top_suppliers_title',
                                'Топ поставщиков по количеству выигранных тендеров') }}</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ t('admin.analytics.top_suppliers.col_name', 'Поставщик') }}</th>
                                        <th class="text-end">{{ t('admin.analytics.top_suppliers.col_wins',
                                            'Выиграно тендеров') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="topSuppliers.length === 0">
                                        <td colspan="2" class="text-center text-muted py-4">{{ t('common.no_data',
                                            'Нет данных') }}</td>
                                    </tr>
                                    <tr v-for="(s, idx) in topSuppliers" :key="`${s.name}-${idx}`">
                                        <td>{{ s.name }}</td>
                                        <td class="text-end">{{ formatNumber(s.won_tenders_count || 0) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-dashboard .welcome-card {
    background: linear-gradient(100deg, #f8fbff, #eef4ff);
    border: none;
}

.badge {
    font-weight: 500;
}
</style>
