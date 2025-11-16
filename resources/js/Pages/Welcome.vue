<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

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

const { t } = useTranslations();

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

    <Head :title="t('auth.welcome_title')" />

    <div class="col col-login mx-auto">

      <div v-if="status" class="alert alert-success" role="alert">{{ status }}</div>

      <form class="card" @submit.prevent="submit" autocomplete="off" novalidate>
        <div class="card-body p-6">
          <h2 class="h2 text-center mb-4">{{ t('auth.welcome_heading') }}</h2>

          <div class="form-group">
            <label class="form-label">{{ t('auth.login_email_label') }}</label>
            <input type="email" class="form-control" :placeholder="t('auth.welcome_email_placeholder')"
              v-model="form.email" :class="{ 'is-invalid': form.errors.email }" required autofocus>
            <div class="invalid-feedback" v-if="form.errors.email">{{ form.errors.email }}</div>
          </div>

          <div class="form-group mt-4">
            <label class="form-label">
              {{ t('auth.login_password_label') }}
              <Link v-if="canResetPassword" :href="route('password.request')" class="float-right small">
              {{ t('auth.login_forgot_password') }}
              </Link>
            </label>
            <input type="password" class="form-control" :placeholder="t('auth.welcome_password_placeholder')"
              v-model="form.password" :class="{ 'is-invalid': form.errors.password }" required>
            <div class="invalid-feedback" v-if="form.errors.password">{{ form.errors.password }}</div>
          </div>

          <div class="mb-2 mt-4">
            <label class="form-check">
              <input type="checkbox" class="form-check-input" v-model="form.remember" />
              <span class="form-check-label">{{ t('auth.login_remember_me') }}</span>
            </label>
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
              <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ t('auth.login_button') }}
            </button>
          </div>
        </div>
      </form>

      <div class="text-center text-muted mt-4" v-if="canRegister">
        {{ t('auth.login_no_account') }}
        <Link :href="route('register')">{{ t('auth.login_register_link') }}</Link>
      </div>
    </div>
  </GuestLayout>
</template>