<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const proposal = page.props.proposal;
const cnyRubRate = computed(() => {
    const raw = Number(page.props.cny_rub_rate ?? 0);

    if (!Number.isFinite(raw) || raw <= 0) {
        return 0;
    }

    return raw;
});
const { t } = useTranslations();

const formatDate = (value) => {
    if (!value) return '-';
    const d = new Date(value);
    const pad = (n) => String(n).padStart(2, '0');
    return `${d.getFullYear()}.${pad(d.getMonth() + 1)}.${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

const statusLabel = computed(() => {
    const s = proposal?.status;
    if (s === 'submitted') return t('proposals.status_submitted', 'Отправлено');
    if (s === 'draft') return t('proposals.status_draft', 'Черновик');
    if (s === 'withdrawn') return t('proposals.status_withdrawn', 'Отозвано');
    if (s === 'approved') return t('proposals.status_approved', 'Принято');
    if (s === 'rejected') return t('proposals.status_rejected', 'Отклонено');
    return s || '-';
});

const formatNumber = (value) => {
    const num = Number(value);

    if (!Number.isFinite(num)) {
        return '-';
    }

    return num.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatPriceCny = (item) => {
    return formatNumber(item?.price ?? 0);
};

const formatPriceRub = (item) => {
    const rate = cnyRubRate.value;

    if (!rate) {
        return '-';
    }

    const priceCny = Number(item?.price ?? 0);

    if (!Number.isFinite(priceCny)) {
        return '-';
    }

    const baseRub = priceCny * rate;
    const withMarkup = baseRub * 1.31;

    return formatNumber(withMarkup);
};

const backToTender = () => {
    if (proposal?.tender?.id) {
        router.visit(route('admin.tenders.show', { tender: proposal.tender.id }));
    }
};

const approve = () => {
    router.post(route('admin.applications.approve', proposal.id));
};

const reject = () => {
    router.post(route('admin.applications.reject', proposal.id));
};
</script>

<template>
    <AdminLayout>
        <div class="container mb-4">
            <h1 class="h2 mb-3">{{ t('proposals.view_customer_title') }}</h1>

            <div v-if="proposal" class="card mb-3">
                <div class="card-body">
                    <p class="mb-2"><strong>{{ t('tenders.tender_label') }}</strong> {{ proposal.tender?.title }}</p>
                    <p class="mb-2"><strong>{{ t('proposals.field_supplier') }}</strong> {{ proposal.user?.name }}</p>
                    <p class="mb-2"><strong>{{ t('proposals.field_status') }}</strong> {{ statusLabel }}</p>
                    <p class="mb-0"><strong>{{ t('proposals.field_submitted_at') }}</strong>
                        {{ formatDate(proposal.submitted_at) }}</p>
                </div>
            </div>

            <div v-if="proposal?.items?.length" class="table-responsive card-table">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ t('proposals.col_item_title') }}</th>
                            <th>{{ t('proposals.col_quantity') }}</th>
                            <th>{{ t('proposals.col_unit') }}</th>
                            <th>{{ t('proposals.col_price') }}</th>
                            <th>{{ t('proposals.col_comment') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in proposal.items" :key="item.id">
                            <td>{{ item.tender_item?.title }}</td>
                            <td>
                                <span class="badge bg-blue text-light">{{ item.tender_item?.quantity }}</span>
                            </td>
                            <td>{{ item.tender_item?.unit }}</td>
                            <td>
                                <strong>
                                    <span v-if="cnyRubRate">
                                        {{ formatPriceRub(item) }} {{ t('admin.proposals.price_rub_label') }}
                                        ({{ formatPriceCny(item) }} {{ t('admin.proposals.price_cny_label') }})
                                    </span>
                                    <span v-else>
                                        {{ formatPriceCny(item) }} {{ t('admin.proposals.price_cny_label') }}
                                    </span>
                                </strong>
                            </td>
                            <td>{{ item.comment }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-secondary" @click="backToTender">
                    {{ t('proposals.back_to_tender', 'Назад к тендеру') }}
                </button>
                <button class="btn btn-success" @click="approve">
                    {{ t('proposals.action_approve', 'Принять') }}
                </button>
                <button class="btn btn-outline-danger" @click="reject">
                    {{ t('proposals.action_reject', 'Отклонить') }}
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
