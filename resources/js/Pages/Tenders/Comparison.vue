<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const tender = computed(() => page.props.tender);
const comparison = computed(() => page.props.comparison ?? {});

const showLeadersOnly = ref(false);
const sortMode = ref('none'); // none | delta | total

const rows = computed(() => {
    const items = comparison.value.items ?? [];
    const totals = comparison.value.proposal_totals ?? {};
    const result = [];

    for (const item of items) {
        const bestPrice = item.best_price;
        const proposals = item.proposals ?? [];

        for (const p of proposals) {
            const price = Number(p.price ?? 0);
            const delta = bestPrice != null ? price - Number(bestPrice) : null;

            result.push({
                itemId: item.id,
                itemName: item.name,
                quantity: Number(item.quantity ?? 0),
                bestPrice,
                supplier: p.supplier ?? '',
                price,
                delta,
                proposalId: p.proposal_id,
                proposalTotal: totals[p.proposal_id] ?? null,
            });
        }
    }

    return result;
});

const processedRows = computed(() => {
    let data = [...rows.value];

    if (showLeadersOnly.value) {
        data = data.filter((row) => row.delta === null || Math.abs(row.delta) < 1e-9);
    }

    if (sortMode.value === 'delta') {
        data.sort((a, b) => {
            if (a.delta === null && b.delta === null) return 0;
            if (a.delta === null) return 1;
            if (b.delta === null) return -1;
            return a.delta - b.delta;
        });
    } else if (sortMode.value === 'total') {
        data.sort((a, b) => {
            const at = a.proposalTotal ?? Number.POSITIVE_INFINITY;
            const bt = b.proposalTotal ?? Number.POSITIVE_INFINITY;
            if (at === bt) return 0;
            return at - bt;
        });
    }

    return data;
});

const formatNumber = (value) => {
    if (value === null || value === undefined || Number.isNaN(Number(value))) {
        return '-';
    }

    return Number(value).toFixed(2);
};
</script>

<template>
    <AppLayout>

        <Head :title="t('tenders.comparison_title')" />
        <div class="container mb-4">
            <h1 class="h2 mb-3">{{ t('tenders.comparison_title') }}</h1>

            <div v-if="tender" class="mb-3">
                <p><strong>{{ t('tenders.tender_label') }}</strong> {{ tender.title }}</p>
            </div>

            <div class="mb-3">
                <label>
                    <input type="checkbox" v-model="showLeadersOnly">
                    {{ t('tenders.label_show_leaders_only') }}
                </label>

                <div class="mt-2">
                    <span>{{ t('tenders.sort_label') }} </span>
                    <label>
                        <input type="radio" value="none" v-model="sortMode">
                        {{ t('tenders.sort_none') }}
                    </label>
                    <label class="ms-2">
                        <input type="radio" value="delta" v-model="sortMode">
                        {{ t('tenders.sort_delta') }}
                    </label>
                    <label class="ms-2">
                        <input type="radio" value="total" v-model="sortMode">
                        {{ t('tenders.sort_total') }}
                    </label>
                </div>
            </div>

            <div v-if="processedRows.length" class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ t('tenders.col_item_title') }}</th>
                            <th>{{ t('tenders.col_quantity') }}</th>
                            <th>{{ t('tenders.col_supplier') }}</th>
                            <th>{{ t('tenders.col_price') }}</th>
                            <th>{{ t('tenders.col_best_price') }}</th>
                            <th>{{ t('tenders.col_difference') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in processedRows" :key="`${row.itemId}-${row.proposalId}`">
                            <td>{{ row.itemName }}</td>
                            <td><span class="badge bg-blue text-light">{{ row.quantity }}</span></td>
                            <td>{{ row.supplier }}</td>
                            <td>
                                <span v-if="row.delta === null || Math.abs(row.delta) < 1e-9"
                                    class="badge bg-success text-light">
                                    {{ formatNumber(row.price) }}
                                </span>
                                <span v-else>{{ formatNumber(row.price) }}</span>
                            </td>
                            <td><strong>{{ formatNumber(row.bestPrice) }}</strong></td>
                            <td>
                                <span v-if="row.delta !== null" :class="row.delta > 0 ? 'text-danger' : 'text-success'">
                                    {{ formatNumber(row.delta) }}
                                </span>
                                <span v-else class="text-muted">-</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else>
                {{ t('tenders.no_proposals_for_comparison') }}
            </div>
        </div>
    </AppLayout>
</template>
