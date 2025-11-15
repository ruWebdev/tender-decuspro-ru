<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const tenders = computed(() => page.props.tenders || []);
const authUser = computed(() => page.props.auth?.user || null);
const isSupplier = computed(() => authUser.value?.role === 'supplier');

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
      <h1 class="h2 mb-3">Активные закупки</h1>

      <div v-if="tenders.length === 0" class="alert alert-info">
        Активные закупки не найдены.
      </div>

      <div v-else class="row">
        <div v-for="tender in tenders" :key="tender.id" class="col-md-6 mb-3">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">{{ tender.title }}</h5>
              <p class="card-text text-muted small">
                <strong>Создано:</strong> {{ formatDate(tender.created_at) }}<br>
                <strong>Актуально до:</strong> {{ formatDate(tender.valid_until) }}<br>
                <strong>Позиций:</strong> {{ tender.items_count || 0 }}
              </p>
              <div class="d-flex gap-2">
                <Link :href="route('tenders.show', { tender: tender.id })" class="btn btn-primary btn-sm">
                Подробнее
                </Link>
                <Link v-if="isSupplier" :href="route('proposals.participate', { tender: tender.id })"
                  class="btn btn-success btn-sm">
                Откликнуться
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-body">
          <h5 class="card-title">Информация</h5>
          <p class="mb-0">Добро пожаловать на тендерную площадку! Здесь вы можете создавать закупки и подавать
            предложения.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
