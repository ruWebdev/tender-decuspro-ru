<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const proposal = page.props.proposal;
const { t } = useTranslations();
</script>

<template>
    <AppLayout>
        <div class="container mb-4">
            <h1 class="h2 mb-3">{{ t('proposals.view_customer_title') }}</h1>

            <div v-if="proposal">
                <p><strong>{{ t('tenders.tender_label') }}</strong> {{ proposal.tender?.title }}</p>
                <p><strong>{{ t('proposals.field_supplier') }}</strong> {{ proposal.user?.name }}</p>
                <p><strong>{{ t('proposals.field_status') }}</strong> {{ proposal.status }}</p>
                <p><strong>{{ t('proposals.field_submitted_at') }}</strong> {{ proposal.submitted_at }}</p>
            </div>

            <div v-if="proposal?.items?.length" class="table-responsive">
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
                            <td><span class="badge bg-blue text-light">{{ item.tender_item?.quantity }}</span></td>
                            <td>{{ item.tender_item?.unit }}</td>
                            <td><strong>{{ item.price }}</strong></td>
                            <td>{{ item.comment }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else>
                {{ t('proposals.no_items') }}
            </div>
        </div>
    </AppLayout>
</template>
