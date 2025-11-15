<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

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
</script>

<template>
    <AppLayout>
        <div class="container mb-4">
            <h1 class="h2 mb-3">Предложения по закупке</h1>

            <div class="mb-3">
                <p><strong>Закупка:</strong> {{ props.tender.title }}</p>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Поставщик</th>
                            <th>Отправлено</th>
                            <th>Позиций</th>
                            <th class="w-25">Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="props.proposals.length === 0">
                            <td colspan="4" class="text-center text-muted">Предложения пока не получены.</td>
                        </tr>
                        <tr v-for="proposal in props.proposals" :key="proposal.id">
                            <td>{{ proposal.user?.name }}</td>
                            <td>{{ proposal.submitted_at }}</td>
                            <td><span class="badge bg-blue text-light">{{ proposal.items_count ?? 0 }}</span></td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <Link :href="route('proposals.view.customer', { proposal: proposal.id })"
                                        class="btn btn-sm btn-ghost-primary">
                                    Открыть
                                    </Link>
                                    <Link v-if="!props.tender?.is_finished"
                                        :href="route('tenders.finish', { tender: props.tender.id })"
                                        class="btn btn-sm btn-ghost-warning">
                                    Выбрать
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
