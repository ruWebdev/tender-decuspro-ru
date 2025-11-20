<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'customer',
    locale: 'ru',
});

const submit = () => {
    form.post(route('admin.users.store'));
};
</script>

<template>
    <AdminLayout>
        <div class="admin-users-create">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.users.create_title') }}</h1>
                <Link :href="route('admin.users.index')" class="btn btn-outline-secondary">
                {{ t('common.back', 'Назад') }}
                </Link>
            </div>

            <form @submit.prevent="submit" class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.users.form.name') }}</label>
                            <input type="text" v-model="form.name" class="form-control"
                                :class="{ 'is-invalid': form.errors.name }" required>
                            <div v-if="form.errors.name" class="invalid-feedback">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.users.form.email') }}</label>
                            <input type="email" v-model="form.email" class="form-control"
                                :class="{ 'is-invalid': form.errors.email }" required>
                            <div v-if="form.errors.email" class="invalid-feedback">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.users.form.password') }}</label>
                            <input type="password" v-model="form.password" class="form-control"
                                :class="{ 'is-invalid': form.errors.password }" required>
                            <div v-if="form.errors.password" class="invalid-feedback">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.users.form.role') }}</label>
                            <select v-model="form.role" class="form-select" :class="{ 'is-invalid': form.errors.role }"
                                required>
                                <option value="customer">{{ t('admin.users.roles.customer') }}</option>
                                <option value="supplier">{{ t('admin.users.roles.supplier') }}</option>
                                <option value="admin">{{ t('admin.users.roles.admin') }}</option>
                                <option value="moderator">{{ t('admin.users.roles.moderator') }}</option>
                            </select>
                            <div v-if="form.errors.role" class="invalid-feedback">
                                {{ form.errors.role }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.users.form.locale') }}</label>
                            <select v-model="form.locale" class="form-select"
                                :class="{ 'is-invalid': form.errors.locale }">
                                <option value="ru">Русский</option>
                                <option value="en">English</option>
                                <option value="cn">中文</option>
                            </select>
                            <div v-if="form.errors.locale" class="invalid-feedback">
                                {{ form.errors.locale }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <Link :href="route('admin.users.index')" class="btn btn-outline-secondary me-2">
                    {{ t('common.cancel', 'Отмена') }}
                    </Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                        {{ t('common.save', 'Сохранить') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
