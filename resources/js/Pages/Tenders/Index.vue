<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const authUser = computed(() => page.props.auth?.user || null);
const isCustomer = computed(() => authUser.value?.role === 'customer');
const isSupplier = computed(() => authUser.value?.role === 'supplier');

// Унифицируем источник данных: для customer приходит массив, для supplier — пагинация
const tendersPaginated = computed(() => {
  const p = page.props.tenders;
  if (p && typeof p === 'object' && Array.isArray(p.data)) return p;
  return { data: Array.isArray(p) ? p : [], links: [] };
});

const currentLocale = computed(() => page.props.locale || 'ru');

const jsLocale = computed(() => {
  if (currentLocale.value === 'en') {
    return 'en-US';
  }
  if (currentLocale.value === 'cn') {
    return 'zh-CN';
  }
  return 'ru-RU';
});

const formatDate = (value) => {
  if (!value) return '-';
  return new Date(value).toLocaleString(jsLocale.value);
};
</script>

<template>
  <AppLayout>
    <div class="container mb-4">
      <h1 class="h2 mb-3">{{ t('tenders.index_title') }}</h1>

      <div class="mb-3 d-flex justify-content-between align-items-center">
        <span class="text-muted" v-if="isCustomer">{{ t('tenders.index_total') }} {{ tendersPaginated.data.length
        }}</span>
        <span class="text-muted" v-else>{{ t('tenders.index_title') }}</span>
        <Link v-if="isCustomer" :href="route('tenders.create')" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
          stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
          <path d="M12 5l0 14" />
          <path d="M5 12l14 0" />
        </svg>
        {{ t('tenders.button_create') }}
        </Link>
      </div>

      <div v-if="tendersPaginated.data.length === 0" class="alert alert-info" role="alert">
        <div class="d-flex">
          <div>
            {{ t('tenders.index_empty') }}
          </div>
        </div>
      </div>

      <div v-else class="table-responsive">
        <div class="card">
          <table class="table table-vcenter card-table">
            <thead>
              <tr>
                <th>{{ t('tenders.col_title') }}</th>
                <th>{{ t('tenders.col_created_at') }}</th>
                <th>{{ t('tenders.col_valid_until') }}</th>
                <th>{{ t('tenders.col_status') }}</th>
                <th>{{ t('tenders.col_items') }}</th>
                <th class="w-25">{{ t('tenders.col_actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="tender in tendersPaginated.data" :key="tender.id">
                <td>
                  <div>
                    <a href="#" class="text-reset text-decoration-none">{{ tender.title }}</a>
                  </div>
                </td>
                <td>{{ formatDate(tender.created_at) }}</td>
                <td>{{ formatDate(tender.valid_until) }}</td>
                <td>
                  <span v-if="tender.is_finished" class="badge bg-success text-light">Завершен</span>
                  <span v-else class="badge bg-primary text-light">{{ t('tenders.status_open') }}</span>
                </td>
                <td>{{ tender.items_count ?? 0 }}</td>
                <td>
                  <div class="btn-list flex-nowrap">
                    <Link :href="route('tenders.show', { tender: tender.id })" class="btn btn-sm btn-ghost-primary">
                    {{ t('tenders.action_open') }}
                    </Link>
                    <!-- Кнопки только для заказчика -->
                    <Link v-if="isCustomer && !tender.is_finished"
                      :href="route('proposals.index.customer', { tender: tender.id })"
                      class="btn btn-sm btn-ghost-info">
                    {{ t('tenders.action_proposals') }} ({{ tender.proposals_count ?? 0 }})
                    </Link>
                    <Link v-if="isCustomer" :href="route('tenders.comparison', { tender: tender.id })"
                      class="btn btn-sm btn-ghost-secondary">
                    {{ t('tenders.action_compare') }}
                    </Link>
                    <!-- Для поставщика — переход к своему предложению, если есть -->
                    <Link v-if="isSupplier && tender.my_proposal_id"
                      :href="route('proposals.participate', { tender: tender.id })"
                      class="btn btn-sm btn-ghost-secondary">
                    {{ t('proposals.action_edit', 'Редактировать') }}
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Пагинация только если пришла пагинация (для поставщика) -->
      <div v-if="tendersPaginated.links && tendersPaginated.links.length > 3" class="card-footer mt-2">
        <nav class="d-flex justify-content-center">
          <ul class="pagination mb-0">
            <li v-for="(link, index) in tendersPaginated.links" :key="index" class="page-item"
              :class="{ active: link.active, disabled: !link.url }">
              <Link v-if="link.url" :href="link.url" class="page-link" v-html="link.label" />
              <span v-else class="page-link" v-html="link.label" />
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </AppLayout>
</template>
