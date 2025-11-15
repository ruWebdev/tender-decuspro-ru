<script setup>
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

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
      <h1 class="h2 mb-3">Профиль поставщика</h1>

      <div class="card">
        <div class="card-body">
          <div v-if="!isEditing" class="mb-3">
            <p><strong>Пользователь:</strong> {{ user?.name }}</p>
            <p><strong>Email:</strong> {{ user?.email }}</p>
            <button @click="isEditing = true" class="btn btn-primary">
              Редактировать профиль
            </button>
          </div>

          <form v-else @submit.prevent="submit">
            <div class="mb-3">
              <label class="form-label">Название компании</label>
              <input type="text" class="form-control" v-model="form.company_name" placeholder="Название вашей компании">
            </div>

            <div class="mb-3">
              <label class="form-label">Телефон</label>
              <input type="tel" class="form-control" v-model="form.contact_data.phone" placeholder="+7 (999) 999-99-99">
            </div>

            <div class="mb-3">
              <label class="form-label">Адрес</label>
              <input type="text" class="form-control" v-model="form.contact_data.address" placeholder="Адрес компании">
            </div>

            <div class="mb-3">
              <label class="form-label">Веб-сайт</label>
              <input type="url" class="form-control" v-model="form.contact_data.website"
                placeholder="https://example.com">
            </div>

            <div class="mb-3">
              <button type="submit" class="btn btn-primary" :disabled="form.processing">
                Сохранить
              </button>
              <button type="button" @click="isEditing = false" class="btn btn-secondary ms-2">
                Отмена
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
