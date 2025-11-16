<script setup>
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const { t } = useTranslations();

const form = useForm({
  company_name: '',
  contact_data: {
    phone: '',
    address: '',
    website: '',
  },
});

const isEditing = ref(false);

const submit = () => {
  // Здесь будет логика сохранения профиля поставщика
  isEditing.value = false;
};
</script>

<template>
  <AppLayout>
    <div class="container mb-4">
      <h1 class="h2 mb-3">{{ t('common.supplier_profile_title') }}</h1>

      <div class="card">
        <div class="card-body">
          <div v-if="!isEditing" class="mb-3">
            <p><strong>{{ t('common.user') }}:</strong> {{ user?.name }}</p>
            <p><strong>{{ t('common.email') }}:</strong> {{ user?.email }}</p>
            <button @click="isEditing = true" class="btn btn-primary">
              {{ t('common.edit') }}
            </button>
          </div>

          <form v-else @submit.prevent="submit">
            <div class="mb-3">
              <label class="form-label">{{ t('common.company_name') }}</label>
              <input type="text" class="form-control" v-model="form.company_name"
                :placeholder="t('common.company_name_placeholder')">
            </div>

            <div class="mb-3">
              <label class="form-label">{{ t('common.phone') }}</label>
              <input type="tel" class="form-control" v-model="form.contact_data.phone"
                :placeholder="t('common.phone_placeholder')">
            </div>

            <div class="mb-3">
              <label class="form-label">{{ t('common.address') }}</label>
              <input type="text" class="form-control" v-model="form.contact_data.address"
                :placeholder="t('common.address_placeholder')">
            </div>

            <div class="mb-3">
              <label class="form-label">{{ t('common.website') }}</label>
              <input type="url" class="form-control" v-model="form.contact_data.website"
                :placeholder="t('common.website_placeholder')">
            </div>

            <div class="mb-3">
              <button type="submit" class="btn btn-primary" :disabled="form.processing">
                {{ t('common.save') }}
              </button>
              <button type="button" @click="isEditing = false" class="btn btn-secondary ms-2">
                {{ t('common.cancel') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
