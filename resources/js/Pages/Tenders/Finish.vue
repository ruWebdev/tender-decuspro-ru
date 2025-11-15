<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();

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

    router.post(route('tenders.finish.submit', tender.value.id), {
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
    <AppLayout>
        <div class="container mb-4">
            <h1 class="h2 mb-3">Выбор победителя</h1>

            <div v-if="tender" class="mb-3">
                <p><strong>Закупка:</strong> {{ tender.title }}</p>
            </div>

            <div v-if="loadingTotals" class="mb-3">
                Загрузка итоговых сумм предложений...
            </div>

            <div v-if="rows.length" class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Поставщик</th>
                            <th>Итоговая сумма</th>
                            <th>Разница с лидером</th>
                            <th class="w-25">Действие</th>
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
                                        Выбор...
                                    </span>
                                    <span v-else>Выбрать победителем</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else>
                Предложения для выбора победителя отсутствуют.
            </div>
        </div>
    </AppLayout>
</template>
