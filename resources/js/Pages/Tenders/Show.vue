<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const tender = computed(() => page.props.tender);
const authUser = computed(() => page.props.auth?.user || null);
const isSupplier = computed(() => authUser.value?.role === 'supplier');
const isCustomer = computed(() => authUser.value?.role === 'customer');

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

const tenderDescription = computed(() => {
  if (!tender.value) {
    return '';
  }

  if (currentLocale.value === 'en') {
    return tender.value.description_en || tender.value.description || '';
  }

  if (currentLocale.value === 'cn') {
    return tender.value.description_cn || tender.value.description || '';
  }

  return tender.value.description || '';
});

const itemTitle = (item) => {
  if (currentLocale.value === 'en') {
    return item.name_en || item.title;
  }

  if (currentLocale.value === 'cn') {
    return item.name_cn || item.title;
  }

  return item.title;
};

const formatDate = (value) => {
  if (!value) {
    return '-';
  }
  return new Date(value).toLocaleString(jsLocale.value);
};
</script>

<template>
  <AppLayout>
    <div class="container mb-4">
      <h1 class="h2 mb-3">{{ t('tenders.show_title') }}</h1>

      <div v-if="tender" class="card mb-4">
        <div class="card-body">
          <h2 class="h4 mb-3">{{ tender.title }}</h2>

          <div class="row mb-3">
            <div class="col-md-6">
              <p class="mb-1">
                <strong>{{ t('tenders.field_status') }}</strong>
                <span v-if="tender.is_finished" class="badge bg-success text-light">
                  {{ t('tenders.status_finished') }}
                </span>
                <span v-else class="badge bg-primary text-light">
                  {{ t('tenders.status_open') }}
                </span>
              </p>
              <p class="mb-1">
                <strong>{{ t('tenders.field_created_at') }}</strong> {{ formatDate(tender.created_at) }}
              </p>
              <p class="mb-1">
                <strong>{{ t('tenders.field_valid_until') }}</strong> {{ formatDate(tender.valid_until) }}
              </p>
            </div>
            <div class="col-md-6">
              <p v-if="tender.is_finished" class="text-success">
                <strong>{{ t('tenders.status_finished_label') }}</strong>
              </p>
              <p v-else class="text-info">
                <strong>{{ t('tenders.status_open_label') }}</strong>
              </p>
            </div>
          </div>

          <div class="mb-3">
            <strong>{{ t('tenders.field_description') }}</strong>
            <p>{{ tenderDescription || t('tenders.no_description') }}</p>
          </div>

          <div v-if="tender.hidden_comment" class="mb-3">
            <strong>{{ t('tenders.field_hidden_comment') }}</strong>
            <p>{{ tender.hidden_comment }}</p>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">
          <h3 class="h5 mb-0">{{ t('tenders.positions_title') }}</h3>
        </div>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead>
              <tr>
                <th>{{ t('tenders.col_item_title') }}</th>
                <th>{{ t('tenders.col_quantity') }}</th>
                <th>{{ t('tenders.col_unit') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in tender.items" :key="item.id">
                <td>{{ itemTitle(item) }}</td>
                <td>{{ item.quantity }}</td>
                <td>{{ item.unit || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="mb-3 d-flex gap-2">
        <Link :href="route('tenders.index')" class="btn btn-secondary">
        {{ t('tenders.back_to_list') }}
        </Link>

        <!-- Для поставщика: кнопка откликнуться -->
        <Link v-if="isSupplier && !tender.is_finished" :href="route('proposals.participate', { tender: tender.id })"
          class="btn btn-success">
        {{ t('tenders.action_participate') }}
        </Link>

        <Link v-if="isCustomer" :href="route('tenders.comparison', { tender: tender.id })"
          class="btn btn-outline-primary">
        {{ t('tenders.action_compare_tender') }}
        </Link>

        <Link v-if="isCustomer" :href="route('proposals.index.customer', { tender: tender.id })"
          class="btn btn-outline-secondary">
        {{ t('tenders.action_view_proposals') }}
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
