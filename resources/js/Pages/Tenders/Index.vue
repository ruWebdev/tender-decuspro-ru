<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const authUser = computed(() => page.props.auth?.user || null);
const roleNames = computed(() => authUser.value?.role_names || []);
const hasRole = (role) => roleNames.value.includes(role);
const isCustomer = computed(() => hasRole('customer'));
const isSupplier = computed(() => hasRole('supplier'));

// Источник данных: для customer — массив; для supplier — пагинация с бэкенда
const source = computed(() => page.props.tenders);
const itemsPerPage = 10;
const currentPage = ref(1);

const customerTotal = computed(() => (Array.isArray(source.value) ? source.value.length : 0));
const customerPages = computed(() => (customerTotal.value ? Math.ceil(customerTotal.value / itemsPerPage) : 1));
const customerSlice = computed(() => {
  if (!Array.isArray(source.value)) return [];
  const start = (currentPage.value - 1) * itemsPerPage;
  return source.value.slice(start, start + itemsPerPage);
});

const tendersPaginated = computed(() => {
  // Для supplier уже приходит {data, links}
  if (source.value && typeof source.value === 'object' && Array.isArray(source.value.data)) return source.value;
  // Для customer смоделируем объект пагинации
  return { data: customerSlice.value, links: [] };
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

const formatDateOnly = (value) => {
  if (!value) return '-';
  return new Date(value).toLocaleDateString(jsLocale.value, { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const formatTime = (tender) => {
  const time = tender?.valid_until_time;
  if (time && /^\d{2}:\d{2}$/.test(time)) return time;
  const d = tender?.valid_until ? new Date(tender.valid_until) : null;
  if (!d) return '';
  return d.toLocaleTimeString(jsLocale.value, { hour: '2-digit', minute: '2-digit' });
};

const getTenderDescription = (tender) => {
  if (currentLocale.value === 'en' && tender.description_en) return tender.description_en;
  if (currentLocale.value === 'cn' && tender.description_cn) return tender.description_cn;
  return tender.description || '';
};

const descriptionSnippet = (tender) => {
  const text = (getTenderDescription(tender) || '').toString().trim();
  if (text.length <= 500) return text;
  const slice = text.slice(0, 500);
  const lastSpace = slice.lastIndexOf(' ');
  const safe = lastSpace > 0 ? slice.slice(0, lastSpace) : slice;
  return safe + '…';
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
                <th class="w-1">{{ t('tenders.col_actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="tender in tendersPaginated.data" :key="tender.id">
                <td>
                  <div>
                    <a href="#" class="text-reset text-decoration-none">{{ tender.title }}</a>
                    <div class="small text-muted mt-1" v-if="descriptionSnippet(tender)">
                      {{ descriptionSnippet(tender) }}
                    </div>
                  </div>
                </td>
                <td>{{ formatDate(tender.created_at) }}</td>
                <td>
                  {{ formatDateOnly(tender.valid_until) }}
                  <span v-if="formatTime(tender)" class="text-muted">{{ ' ' + formatTime(tender) }}</span>
                </td>
                <td>
                  <span v-if="tender.is_finished" class="badge bg-success text-light">{{ t('tenders.status_finished')
                  }}</span>
                  <span v-else class="badge bg-primary text-light">{{ t('tenders.status_open') }}</span>
                </td>
                <td>{{ tender.items_count ?? 0 }}</td>
                <td>
                  <div class="btn-list flex-nowrap">
                    <Link :href="route('tenders.show', { tender: tender.id })" class="btn btn-sm btn-ghost-primary">
                    {{ t('tenders.action_open') }}
                    </Link>
                    <Link v-if="isCustomer && !tender.is_finished"
                      :href="route('proposals.index.customer', { tender: tender.id })"
                      class="btn btn-sm btn-ghost-info">
                    {{ t('tenders.action_proposals') }} ({{ tender.proposals_count ?? 0 }})
                    </Link>
                    <Link v-if="isCustomer" :href="route('tenders.comparison', { tender: tender.id })"
                      class="btn btn-sm btn-ghost-secondary">
                    {{ t('tenders.action_compare') }}
                    </Link>
                    <Link v-if="isSupplier && tender.my_proposal_id"
                      :href="route('proposals.participate', { tender: tender.id })"
                      class="btn btn-sm btn-ghost-secondary">
                    {{ t('proposals.action_edit', 'Редактировать') }}
                    </Link>
                    <Link v-if="isSupplier && !tender.is_finished && !tender.my_proposal_id"
                      :href="route('proposals.participate', { tender: tender.id })" class="btn btn-sm btn-success">
                    {{ t('tenders.action_participate') }}
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Клиентская пагинация для заказчика -->
      <div v-if="isCustomer && customerPages > 1" class="card-footer mt-2 d-flex justify-content-center">
        <ul class="pagination mb-0">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button class="page-link" type="button" @click="currentPage = Math.max(1, currentPage - 1)">«</button>
          </li>
          <li v-for="p in customerPages" :key="p" class="page-item" :class="{ active: p === currentPage }">
            <button class="page-link" type="button" @click="currentPage = p">{{ p }}</button>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === customerPages }">
            <button class="page-link" type="button"
              @click="currentPage = Math.min(customerPages, currentPage + 1)">»</button>
          </li>
        </ul>
      </div>

      <!-- Серверная пагинация (для поставщика) -->
      <div v-if="isSupplier && tendersPaginated.links && tendersPaginated.links.length > 3" class="card-footer mt-2">
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
