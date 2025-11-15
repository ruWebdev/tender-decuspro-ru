<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
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
            <h1 class="h2 mb-3">Лучшие цены по позициям</h1>

            <div v-if="tender" class="mb-3">
                <p><strong>Закупка:</strong> {{ tender.title }}</p>
            </div>

            <div v-if="rows.length" class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Позиция</th>
                            <th>Лучшая цена</th>
                            <th>Ваша цена</th>
                            <th>Разница</th>
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
                Нет данных по лучшим ценам.
            </div>
        </div>
    </AppLayout>
</template>
