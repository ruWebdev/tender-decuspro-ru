<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const tender = computed(() => page.props.tender);
const authUser = computed(() => page.props.auth?.user || null);
const isSupplier = computed(() => authUser.value?.role === 'supplier');
const isCustomer = computed(() => authUser.value?.role === 'customer');

const formatDate = (value) => {
  if (!value) {
    return '-';
  }
  return new Date(value).toLocaleString('ru-RU');
};
</script>

<template>
  <AppLayout>
    <div class="container mb-4">
      <h1 class="h2 mb-3">Детали закупки</h1>

      <div v-if="tender" class="card mb-4">
        <div class="card-body">
          <h2 class="h4 mb-3">{{ tender.title }}</h2>

          <div class="row mb-3">
            <div class="col-md-6">
              <p><strong>Статус:</strong> {{ tender.status }}</p>
              <p><strong>Создано:</strong> {{ formatDate(tender.created_at) }}</p>
              <p><strong>Актуально до:</strong> {{ formatDate(tender.valid_until) }}</p>
            </div>
            <div class="col-md-6">
              <p v-if="tender.is_finished" class="text-success">
                <strong>✓ Тендер завершён</strong>
              </p>
              <p v-else class="text-info">
                <strong>Тендер открыт</strong>
              </p>
            </div>
          </div>

          <div class="mb-3">
            <strong>Описание:</strong>
            <p>{{ tender.description || 'Нет описания' }}</p>
          </div>

          <div v-if="tender.hidden_comment" class="mb-3">
            <strong>Скрытый комментарий:</strong>
            <p>{{ tender.hidden_comment }}</p>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">
          <h3 class="h5 mb-0">Позиции закупки</h3>
        </div>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead>
              <tr>
                <th>Название</th>
                <th>Количество</th>
                <th>Ед. изм.</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in tender.items" :key="item.id">
                <td>{{ item.title }}</td>
                <td>{{ item.quantity }}</td>
                <td>{{ item.unit || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="mb-3 d-flex gap-2">
        <Link :href="route('tenders.index')" class="btn btn-secondary">
        Вернуться к списку
        </Link>

        <!-- Для поставщика: кнопка откликнуться -->
        <Link v-if="isSupplier && !tender?.is_finished" :href="route('proposals.participate', { tender: tender.id })"
          class="btn btn-success">
        Откликнуться на тендер
        </Link>

        <!-- Для заказчика: кнопки управления -->
        <Link v-if="isCustomer" :href="route('tenders.comparison', { tender: tender.id })" class="btn btn-info">
        Сравнить предложения
        </Link>

        <Link v-if="isCustomer && !tender?.is_finished" :href="route('proposals.index.customer', { tender: tender.id })"
          class="btn btn-primary">
        Просмотреть предложения
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
