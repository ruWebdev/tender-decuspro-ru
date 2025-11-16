<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const tenders = computed(() => page.props.tenders || []);
const authUser = computed(() => page.props.auth?.user || null);
const isSupplier = computed(() => authUser.value?.role === 'supplier');

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
  if (!value) {
    return '-';
  }
  return new Date(value).toLocaleString(jsLocale.value);
};
</script>

<template>
  <AppLayout>
    <div class="container mb-4">
      <h1 class="h2 mb-3">{{ t('home.title') }}</h1>

      <div v-if="tenders.length === 0" class="alert alert-info">
        {{ t('home.no_tenders') }}
      </div>

      <div v-else class="row">
        <div v-for="tender in tenders" :key="tender.id" class="col-md-6 mb-3">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">{{ tender.title }}</h5>
              <p class="card-text text-muted small">
                <strong>{{ t('tenders.field_created_at') }}</strong> {{ formatDate(tender.created_at) }}<br>
                <strong>{{ t('tenders.field_valid_until') }}</strong> {{ formatDate(tender.valid_until) }}<br>
                <strong>{{ t('tenders.col_items') }}</strong> {{ tender.items_count || 0 }}
              </p>
              <div class="d-flex gap-2">
                <Link :href="route('tenders.show', { tender: tender.id })" class="btn btn-primary btn-sm">
                {{ t('home.button_details') }}
                </Link>
                <Link v-if="isSupplier" :href="route('proposals.participate', { tender: tender.id })"
                  class="btn btn-success btn-sm">
                {{ t('home.button_participate') }}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-body">
          <h5 class="card-title">{{ t('home.info_title') }}</h5>
          <p class="mb-0">{{ t('home.info_text') }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
