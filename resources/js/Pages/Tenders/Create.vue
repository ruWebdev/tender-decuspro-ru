<script setup>
import { onBeforeUnmount, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const form = useForm({
  title: '',
  description: '',
  hidden_comment: '',
  valid_until: '',
  valid_until_time: '',
  items: [
    { title: '', quantity: '', unit: '' },
  ],
});

const rawList = ref('');
const isAutofillLoading = ref(false);
const autofillMessage = ref('');

const addItem = () => {
  form.items.push({ title: '', quantity: '', unit: '' });
};

const removeItem = (index) => {
  if (form.items.length <= 1) {
    return;
  }

  form.items.splice(index, 1);
};

const submit = () => {
  form.post(route('tenders.store'));
};

const autofill = async () => {
  if (!rawList.value.trim()) {
    autofillMessage.value = t('tenders.autofill_failed');
    return;
  }

  autofillMessage.value = '';
  isAutofillLoading.value = true;

  try {
    const response = await axios.post(route('tenders.autofill'), {
      text: rawList.value,
    });

    const items = response.data?.items ?? [];

    if (Array.isArray(items) && items.length > 0) {
      form.items = items.map((item) => ({
        title: item.title ?? '',
        quantity: item.quantity ?? '',
        unit: item.unit ?? '',
      }));

      autofillMessage.value = t('tenders.autofill_filled');
    } else {
      autofillMessage.value = t('tenders.autofill_failed');
    }
  } catch (error) {
    autofillMessage.value = t('tenders.autofill_error');
  } finally {
    isAutofillLoading.value = false;
  }
};

onBeforeUnmount(() => {
  // No need to stop polling here, as we're not using it anymore
});
</script>

<template>
  <AppLayout>
    <div class="container mb-4">
      <h1 class="h2 mb-3">{{ t('tenders.create_title') }}</h1>

      <form @submit.prevent="submit" class="card">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">{{ t('tenders.label_title') }}</label>
            <input type="text" v-model="form.title" class="form-control" :class="{ 'is-invalid': form.errors.title }">
            <div v-if="form.errors.title" class="invalid-feedback">
              {{ form.errors.title }}
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ t('tenders.label_description') }}</label>
            <textarea rows="3" v-model="form.description" class="form-control"
              :class="{ 'is-invalid': form.errors.description }"></textarea>
            <div v-if="form.errors.description" class="invalid-feedback">
              {{ form.errors.description }}
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ t('tenders.label_hidden_comment') }}</label>
            <textarea rows="3" v-model="form.hidden_comment" class="form-control"
              :class="{ 'is-invalid': form.errors.hidden_comment }"></textarea>
            <div v-if="form.errors.hidden_comment" class="invalid-feedback">
              {{ form.errors.hidden_comment }}
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ t('tenders.label_valid_until') }}</label>
            <input type="date" v-model="form.valid_until" class="form-control"
              :class="{ 'is-invalid': form.errors.valid_until }">
            <div v-if="form.errors.valid_until" class="invalid-feedback">
              {{ form.errors.valid_until }}
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ t('tenders.label_valid_until_time', 'Время окончания') }}</label>
            <input type="time" v-model="form.valid_until_time" class="form-control"
              :class="{ 'is-invalid': form.errors.valid_until_time }">
            <div v-if="form.errors.valid_until_time" class="invalid-feedback">
              {{ form.errors.valid_until_time }}
            </div>
          </div>

          <!-- Позиции тендера -->
          <div class="mb-3">
            <h3 class="h5 mb-3">{{ t('tenders.positions_block_title') }}</h3>

            <div v-if="form.errors.items" class="alert alert-danger">
              {{ form.errors.items }}
            </div>

            <div class="table-responsive mb-3">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>{{ t('tenders.col_item_title') }}</th>
                    <th>{{ t('tenders.col_quantity') }}</th>
                    <th>{{ t('tenders.col_unit') }}</th>
                    <th class="w-10"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in form.items" :key="index">
                    <td>
                      <input type="text" v-model="item.title" class="form-control form-control-sm"
                        :class="{ 'is-invalid': form.errors[`items.${index}.title`] }">
                      <div v-if="form.errors[`items.${index}.title`]" class="invalid-feedback">
                        {{ form.errors[`items.${index}.title`] }}
                      </div>
                    </td>
                    <td>
                      <input type="number" step="0.01" min="0" v-model="item.quantity"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': form.errors[`items.${index}.quantity`] }">
                      <div v-if="form.errors[`items.${index}.quantity`]" class="invalid-feedback">
                        {{ form.errors[`items.${index}.quantity`] }}
                      </div>
                    </td>
                    <td>
                      <input type="text" v-model="item.unit" class="form-control form-control-sm"
                        :class="{ 'is-invalid': form.errors[`items.${index}.unit`] }">
                      <div v-if="form.errors[`items.${index}.unit`]" class="invalid-feedback">
                        {{ form.errors[`items.${index}.unit`] }}
                      </div>
                    </td>
                    <td>
                      <button type="button" @click="removeItem(index)" class="btn btn-sm btn-ghost-danger"
                        :disabled="form.items.length <= 1">
                        ✕
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <button type="button" @click="addItem" class="btn btn-sm btn-secondary">
              {{ t('tenders.button_add_item') }}
            </button>
          </div>

          <!-- Автозаполнение из текста -->
          <div class="mb-3">
            <h3 class="h5 mb-3">{{ t('tenders.autofill_block_title') }}</h3>

            <div class="mb-3">
              <textarea rows="4" v-model="rawList" class="form-control"
                :placeholder="t('tenders.autofill_placeholder')"></textarea>
            </div>

            <button type="button" @click="autofill" class="btn btn-secondary" :disabled="isAutofillLoading">
              <span v-if="isAutofillLoading">
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                {{ t('tenders.button_autofill_loading') }}
              </span>
              <span v-else>{{ t('tenders.button_autofill') }}</span>
            </button>

            <div v-if="autofillMessage" class="alert alert-info mt-2">
              {{ autofillMessage }}
            </div>
          </div>
        </div>

        <!-- Отправка формы -->
        <div class="card-footer text-end">
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            <span v-if="form.processing">
              <span class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ t('tenders.create_button_processing') }}
            </span>
            <span v-else>{{ t('tenders.create_button') }}</span>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
