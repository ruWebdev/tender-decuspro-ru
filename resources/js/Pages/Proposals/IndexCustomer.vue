<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    tender: {
        type: Object,
        required: true,
    },
    proposals: {
        type: Array,
        default: () => [],
    },
});

const { t } = useTranslations();
</script>

<template>
    <AppLayout>
        <div class="container mb-4">
            <h1 class="h2 mb-3">{{ t('proposals.customer_index_title') }}</h1>

            <div class="mb-3">
                <p><strong>{{ t('tenders.tender_label') }}</strong> {{ props.tender.title }}</p>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ t('proposals.customer_col_supplier') }}</th>
                            <th>{{ t('proposals.customer_col_submitted_at') }}</th>
                            <th>{{ t('proposals.customer_col_items') }}</th>
                            <th class="w-25">{{ t('proposals.customer_col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="props.proposals.length === 0">
                            <td colspan="4" class="text-center text-muted">{{ t('proposals.customer_no_proposals') }}
                            </td>
                        </tr>
                        <tr v-for="proposal in props.proposals" :key="proposal.id">
                            <td>{{ proposal.user?.name }}</td>
                            <td>{{ proposal.submitted_at }}</td>
                            <td><span class="badge bg-blue text-light">{{ proposal.items_count ?? 0 }}</span></td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <Link :href="route('proposals.view.customer', { proposal: proposal.id })"
                                        class="btn btn-sm btn-ghost-primary">
                                    {{ t('proposals.customer_action_open') }}
                                    </Link>
                                    <Link v-if="!props.tender?.is_finished"
                                        :href="route('tenders.finish', { tender: props.tender.id })"
                                        class="btn btn-sm btn-ghost_warning">
                                    {{ t('proposals.customer_action_select') }}
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
