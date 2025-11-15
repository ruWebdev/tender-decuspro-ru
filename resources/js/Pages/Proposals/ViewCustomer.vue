<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const proposal = page.props.proposal;
</script>

<template>
    <AppLayout>
        <div class="container mb-4">
            <h1 class="h2 mb-3">Предложение поставщика</h1>

            <div v-if="proposal">
                <p><strong>Закупка:</strong> {{ proposal.tender?.title }}</p>
                <p><strong>Поставщик:</strong> {{ proposal.user?.name }}</p>
                <p><strong>Статус:</strong> {{ proposal.status }}</p>
                <p><strong>Отправлено:</strong> {{ proposal.submitted_at }}</p>
            </div>

            <div v-if="proposal?.items?.length" class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Позиция</th>
                            <th>Количество</th>
                            <th>Ед. изм.</th>
                            <th>Цена</th>
                            <th>Комментарий</th>
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
                Нет позиций в предложении.
            </div>
        </div>
    </AppLayout>
</template>
