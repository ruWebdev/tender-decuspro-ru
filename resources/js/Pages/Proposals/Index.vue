<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();
</script>

<template>
  <AppLayout>
    <div class="container mb-4">
      <h1 class="h2 mb-3">{{ t('proposals.index_title') }}</h1>

      <div class="alert alert-info">
        <p class="mb-0">{{ t('proposals.index_info') }}
          <Link href="/dashboard">{{ t('common.cabinet_title') }}</Link>.
        </p>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">{{ t('home.title') }}</h5>
          <p class="card-text text-muted">{{ t('proposals.index_active_info') }}</p>
          <Link href="/" class="btn btn-primary">
          {{ t('common.go_to_tenders') }}
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
