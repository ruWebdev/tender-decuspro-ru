<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const user = computed(() => page.props.auth?.user);
const roles = computed(() => user.value?.role_names || []);
const isCustomer = computed(() => roles.value.includes('customer'));
const isSupplier = computed(() => roles.value.includes('supplier'));

const moderation = computed(() => page.props.moderation || null);
const supplierDocuments = computed(() => page.props.supplier_documents || {});

const moderationStatusLabel = computed(() => {
  if (!moderation.value) {
    return t('common.moderation_unknown');
  }

  switch (moderation.value.status) {
    case 'waiting_documents':
      return t('common.moderation_waiting_documents');
    case 'in_review':
      return t('common.moderation_in_review');
    case 'approved':
      return t('common.moderation_approved');
    case 'rejected':
      return t('common.moderation_rejected');
    default:
      return t('common.moderation_unknown');
  }
});

const moderationStatusBadgeClass = computed(() => {
  if (!moderation.value) {
    return 'bg-secondary text-light';
  }

  switch (moderation.value.status) {
    case 'waiting_documents':
      return 'bg-secondary text-light';
    case 'in_review':
      return 'bg-info text-light';
    case 'approved':
      return 'bg-success text-light';
    case 'rejected':
      return 'bg-danger text-light';
    default:
      return 'bg-secondary text-light';
  }
});

const documentConfigs = {
  business_license: {
    label: 'common.doc_business_license',
    description: 'common.doc_business_license_hint',
  },
  tax_certificate: {
    label: 'common.doc_tax_certificate',
    description: 'common.doc_tax_certificate_hint',
  },
  power_of_attorney: {
    label: 'common.doc_power_of_attorney',
    description: 'common.doc_power_of_attorney_hint',
  },
  board_resolution: {
    label: 'common.doc_board_resolution',
    description: 'common.doc_board_resolution_hint',
  },
  passport_director: {
    label: 'common.doc_passport_director',
    description: 'common.doc_passport_director_hint',
  },
  passport_signatory: {
    label: 'common.doc_passport_signatory',
    description: 'common.doc_passport_signatory_hint',
  },
};

const documentsForm = useForm({
  business_license: null,
  tax_certificate: null,
  power_of_attorney: null,
  board_resolution: null,
  passport_director: null,
  passport_signatory: null,
});

const submitDocuments = () => {
  documentsForm.post(route('profile.supplier.documents.upload'), {
    forceFormData: true,
    preserveScroll: true,
  });
};

const docStatusLabel = (status) => {
  if (status === 'pending') {
    return t('common.doc_status_pending', 'На проверке');
  }
  if (status === 'approved') {
    return t('common.doc_status_approved', 'Проверено');
  }
  if (status === 'rejected') {
    return t('common.doc_status_rejected', 'Отклонено');
  }
  return t('common.unknown');
};

const docStatusBadgeClass = (status) => {
  if (status === 'pending') {
    return 'bg-info text-light';
  }
  if (status === 'approved') {
    return 'bg-success text-light';
  }
  if (status === 'rejected') {
    return 'bg-danger text-light';
  }
  return 'bg-secondary text-light';
};
</script>

<template>
  <AppLayout>

    <Head :title="t('common.cabinet_title')" />
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
              <p v-if="!isSupplier">
                <strong>{{ t('common.role') }}:</strong>
                <span v-if="isCustomer" class="badge bg-primary text-light ms-1">
                  {{ t('common.customer') }}
                </span>
              </p>

              <div v-if="isSupplier">
                <p class="mb-1">
                  <strong>{{ t('common.moderation_status') }}:</strong>
                  <span class="badge ms-1" :class="moderationStatusBadgeClass">
                    {{ moderationStatusLabel }}
                  </span>
                </p>
                <p class="text-muted mb-0">
                  {{ t('common.moderation_rejection_hint', 'Статус проверки документов вашей компании.') }}
                </p>

                <div v-if="moderation && moderation.status === 'rejected' && moderation.reason"
                  class="alert alert-danger mt-3">
                  <strong class="d-block mb-1">
                    {{ t('common.moderation_rejection_reason') }}
                  </strong>
                  <span style="white-space: pre-line">
                    {{ moderation.reason }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div v-if="isSupplier" class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">{{ t('common.documents_card_title', 'Документы') }}</h5>
            </div>
            <div class="card-body">
              <p class="text-muted mb-3">
                {{ t('common.documents_card_intro', 'Загрузите базовые документы для проверки контрагента.') }}
              </p>

              <form @submit.prevent="submitDocuments" enctype="multipart/form-data">
                <div v-for="(config, type) in documentConfigs" :key="type" class="mb-3">
                  <label class="form-label">
                    {{ t(config.label) }}
                  </label>
                  <small class="text-muted d-block mb-1">
                    {{ t(config.description) }}
                  </small>

                  <div v-if="supplierDocuments[type]" class="mb-1 small">
                    <span class="d-block">
                      {{ t('common.current_file') }}:
                      <a :href="`/storage/${supplierDocuments[type].file_path}`" target="_blank" rel="noopener">
                        {{ supplierDocuments[type].file_path.split('/').slice(-1)[0] }}
                      </a>
                    </span>
                    <span class="d-block">
                      {{ t('common.status', 'Статус') }}:
                      <span class="badge" :class="docStatusBadgeClass(supplierDocuments[type].status)">
                        {{ docStatusLabel(supplierDocuments[type].status) }}
                      </span>
                    </span>
                  </div>

                  <input class="form-control" type="file" :name="type"
                    @change="(e) => { documentsForm[type] = e.target.files[0]; }">
                </div>

                <button type="submit" class="btn btn-primary" :disabled="documentsForm.processing">
                  {{ t('common.upload_documents', 'Загрузить документы') }}
                </button>
              </form>
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
              <Link href="/tenders" class="list-group-item list-group-item-action">
              {{ t('nav.tenders') }}
              </Link>
              <Link href="/proposals" class="list-group-item list-group-item-action">
              {{ t('nav.my_proposals') }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
