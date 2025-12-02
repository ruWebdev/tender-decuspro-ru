<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const tender = computed(() => page.props.tender);
const proposals = computed(() => page.props.proposals ?? []);

const totals = reactive({});
const loadingTotals = ref(false);
const finishingId = ref(null);

const leaderTotal = computed(() => {
    const values = Object.values(totals)
        .map((v) => Number(v))
        .filter((v) => !Number.isNaN(v));

    if (!values.length) {
        return null;
    }

    return Math.min(...values);
});

const rows = computed(() => {
    return proposals.value.map((proposal) => {
        const total = totals[proposal.id] ?? null;
        const leader = leaderTotal.value;
        const delta = leader !== null && total !== null
            ? Number(total) - Number(leader)
            : null;

        return {
            id: proposal.id,
            supplier: proposal.user?.name ?? '',
            total,
            delta,
        };
    });
});

const formatNumber = (value) => {
    if (value === null || value === undefined || Number.isNaN(Number(value))) {
        return '-';
    }

    return Number(value).toFixed(2);
};

const isLeader = (row) => {
    if (leaderTotal.value === null || row.total === null) {
        return false;
    }

    return Math.abs(Number(row.total) - Number(leaderTotal.value)) < 1e-9;
};

const loadTotals = async () => {
    loadingTotals.value = true;

    try {
        const requests = proposals.value.map(async (proposal) => {
            try {
                const response = await axios.get(
                    route('proposals.total', { proposal: proposal.id }),
                );
                totals[proposal.id] = response.data?.total ?? null;
            } catch (error) {
                totals[proposal.id] = null;
            }
        });

        await Promise.all(requests);
    } finally {
        loadingTotals.value = false;
    }
};

const chooseWinner = (proposalId) => {
    if (!tender.value) {
        return;
    }

    finishingId.value = proposalId;

    router.post(route('admin.tenders.finish.submit', tender.value.id), {
        proposal_id: proposalId,
    }, {
        onFinish: () => {
            finishingId.value = null;
        },
    });
};

onMounted(() => {
    loadTotals();
});
</script>

<template>
    <AdminLayout>
        <div class="container mb-4">
            <h1 class="h2 mb-3">{{ t('tenders.finish_title') }}</h1>

            <div v-if="tender" class="mb-3">
                <p><strong>{{ t('tenders.tender_label') }}</strong> {{ tender.title }}</p>
            </div>

            <div v-if="loadingTotals" class="mb-3">
                {{ t('tenders.loading_totals') }}
            </div>

            <div v-if="rows.length" class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ t('tenders.col_supplier') }}</th>
                            <th>{{ t('tenders.col_total') }}</th>
                            <th>{{ t('tenders.col_delta_leader') }}</th>
                            <th class="w-25">{{ t('tenders.col_finish_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in rows" :key="row.id">
                            <td>{{ row.supplier }}</td>
                            <td>
                                <span v-if="isLeader(row)" class="badge bg-success text-light">
                                    {{ formatNumber(row.total) }}
                                </span>
                                <span v-else><strong>{{ formatNumber(row.total) }}</strong></span>
                            </td>
                            <td>
                                <span v-if="row.delta !== null" :class="row.delta > 0 ? 'text-danger' : 'text-success'">
                                    {{ formatNumber(row.delta) }}
                                </span>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td>
                                <button type="button" @click="chooseWinner(row.id)" class="btn btn-sm btn-success"
                                    :disabled="finishingId === row.id">
                                    <span v-if="finishingId === row.id">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        {{ t('tenders.button_selecting') }}
                                    </span>
                                    <span v-else>{{ t('tenders.button_select_winner') }}</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else>
                {{ t('tenders.no_proposals_for_finish') }}
            </div>
        </div>
    </AdminLayout>
</template>
