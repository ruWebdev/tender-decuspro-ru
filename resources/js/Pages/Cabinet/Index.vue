<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isCustomer = computed(() => user.value?.role === 'customer');
const isSupplier = computed(() => user.value?.role === 'supplier');
const { t } = useTranslations();
</script>

<template>
  <AppLayout>
    <div class="container mb-4">
      <h1 class="h2 mb-3">{{ t('common.cabinet_title') }}</h1>

      <div class="row">
        <div class="col-md-8">
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">{{ t('common.profile') }}</h5>
            </div>
            <div class="card-body">
              <p><strong>{{ t('common.name') }}:</strong> {{ user?.name }}</p>
              <p><strong>{{ t('common.email') }}:</strong> {{ user?.email }}</p>
              <p><strong>{{ t('common.role') }}:</strong>
                <span v-if="isCustomer" class="badge bg-primary">{{ t('common.customer') }}</span>
                <span v-else-if="isSupplier" class="badge bg-success">{{ t('common.supplier') }}</span>
              </p>
            </div>
          </div>

          <div v-if="isCustomer" class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">{{ t('nav.my_tenders') }}</h5>
            </div>
            <div class="card-body">
              <p class="text-muted mb-3">{{ t('common.customer_tenders_info') }}</p>
              <Link href="/tenders" class="btn btn-primary">
              {{ t('common.go_to_tenders') }}
              </Link>
            </div>
          </div>

          <div v-if="isSupplier" class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">{{ t('nav.my_proposals') }}</h5>
            </div>
            <div class="card-body">
              <p class="text-muted mb-3">{{ t('common.supplier_proposals_info') }}</p>
              <Link href="/proposals" class="btn btn-primary">
              {{ t('common.go_to_proposals') }}
              </Link>
            </div>
          </div>

          <div v-if="isSupplier" class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">{{ t('common.profile_company') }}</h5>
            </div>
            <div class="card-body">
              <p class="text-muted mb-3">{{ t('common.supplier_profile_info') }}</p>
              <Link href="/profile/supplier" class="btn btn-primary">
              {{ t('common.edit') }}
              </Link>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">{{ t('common.quick_links') }}</h5>
            </div>
            <div class="list-group list-group-flush">
              <Link href="/" class="list-group-item list-group-item-action">
              {{ t('common.main_page') }}
              </Link>
              <Link href="/proposals" class="list-group-item list-group-item-action">
              {{ t('common.all_proposals') }}
              </Link>
              <a href="#" class="list-group-item list-group-item-action">
                {{ t('common.help') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
