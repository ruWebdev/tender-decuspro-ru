<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  tenders: {
    type: Array,
    default: () => [],
  },
});

const formatDate = (value) => {
  if (!value) {
    return '-';
  }

  return new Date(value).toLocaleString();
};
</script>

<template>
  <AppLayout>
    <div class="container mb-4">
      <h1 class="h2 mb-3">Закупки</h1>

      <div class="mb-3 d-flex justify-content-between align-items-center">
        <span class="text-muted">Всего закупок: {{ props.tenders.length }}</span>
        <Link :href="route('tenders.create')" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
          stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
          <path d="M12 5l0 14" />
          <path d="M5 12l14 0" />
        </svg>
        Создать закупку
        </Link>
      </div>

      <div v-if="props.tenders.length === 0" class="alert alert-info" role="alert">
        <div class="d-flex">
          <div>
            Закупки пока не созданы.
          </div>
        </div>
      </div>

      <div v-else class="table-responsive">
        <table class="table table-vcenter card-table">
          <thead>
            <tr>
              <th>Название</th>
              <th>Дата создания</th>
              <th>Актуально до</th>
              <th>Статус</th>
              <th>Позиций</th>
              <th class="w-25">Действие</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="tender in props.tenders" :key="tender.id">
              <td>
                <div class="text-truncate">
                  <a href="#" class="text-reset text-decoration-none">{{ tender.title }}</a>
                </div>
              </td>
              <td>{{ formatDate(tender.created_at) }}</td>
              <td>{{ formatDate(tender.valid_until) }}</td>
              <td>
                <span v-if="tender.is_finished" class="badge bg-success text-light">Завершен</span>
                <span v-else class="badge bg-primary text-light">Открыт</span>
              </td>
              <td>{{ tender.items_count ?? 0 }}</td>
              <td>
                <div class="btn-list flex-nowrap">
                  <Link :href="route('tenders.show', { tender: tender.id })" class="btn btn-sm btn-ghost-primary">
                  Открыть
                  </Link>
                  <Link v-if="!tender.is_finished" :href="route('proposals.index.customer', { tender: tender.id })"
                    class="btn btn-sm btn-ghost-info">
                  Предложения
                  </Link>
                  <Link :href="route('tenders.comparison', { tender: tender.id })"
                    class="btn btn-sm btn-ghost-secondary">
                  Сравнить
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
