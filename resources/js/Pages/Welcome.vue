<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <GuestLayout>

    <Head title="Добро пожаловать" />

    <div class="col col-login mx-auto">

      <div v-if="status" class="alert alert-success" role="alert">{{ status }}</div>

      <form class="card" @submit.prevent="submit" autocomplete="off" novalidate>
        <div class="card-body p-6">
          <h2 class="h2 text-center mb-4">Вход в аккаунт</h2>

          <div class="form-group">
            <label class="form-label">Электронная почта</label>
            <input type="email" class="form-control" placeholder="Введите email" v-model="form.email"
              :class="{ 'is-invalid': form.errors.email }" required autofocus>
            <div class="invalid-feedback" v-if="form.errors.email">{{ form.errors.email }}</div>
          </div>

          <div class="form-group mt-4">
            <label class="form-label">
              Пароль
              <Link v-if="canResetPassword" :href="route('password.request')" class="float-right small">
              Забыли пароль?
              </Link>
            </label>
            <input type="password" class="form-control" placeholder="Пароль" v-model="form.password"
              :class="{ 'is-invalid': form.errors.password }" required>
            <div class="invalid-feedback" v-if="form.errors.password">{{ form.errors.password }}</div>
          </div>

          <div class="mb-2 mt-4">
            <label class="form-check">
              <input type="checkbox" class="form-check-input" v-model="form.remember" />
              <span class="form-check-label">Запомнить меня на этом устройстве</span>
            </label>
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
              <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
              Войти
            </button>
          </div>
        </div>
      </form>

      <div class="text-center text-muted mt-4" v-if="canRegister">
        Еще нет аккаунта?
        <Link :href="route('register')">Зарегистрироваться</Link>
      </div>
    </div>
  </GuestLayout>
</template>