<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const tender = page.props.tender;
const items = page.props.items || {};

const rows = Object.values(items);

const formatNumber = (value) => {
    if (value === null || value === undefined || Number.isNaN(Number(value))) {
        return '-';
    }

    return Number(value).toFixed(2);
};
</script>

<template>
    <AppLayout>
        <div class="container mb-4">
            <h1 class="h2 mb-3">{{ t('tenders.best_prices_title') }}</h1>

            <div v-if="tender" class="mb-3">
                <p><strong>{{ t('tenders.tender_label') }}</strong> {{ tender.title }}</p>
            </div>

            <div v-if="rows.length" class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ t('tenders.col_item_title') }}</th>
                            <th>{{ t('tenders.col_best_price') }}</th>
                            <th>{{ t('tenders.col_my_price') }}</th>
                            <th>{{ t('tenders.col_difference') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in rows" :key="row.id">
                            <td>{{ row.name }}</td>
                            <td><strong>{{ formatNumber(row.best_price) }}</strong></td>
                            <td>
                                <span
                                    v-if="row.best_price !== null && row.my_price !== null && Math.abs(row.best_price - row.my_price) < 1e-9"
                                    class="badge bg-success text-light">
                                    {{ formatNumber(row.my_price) }}
                                </span>
                                <span v-else>{{ formatNumber(row.my_price) }}</span>
                            </td>
                            <td
                                :class="row.difference && parseFloat(row.difference) > 0 ? 'text-danger' : 'text-success'">
                                {{ row.difference ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else>
                {{ t('tenders.no_best_prices') }}
            </div>
        </div>
    </AppLayout>
</template>
