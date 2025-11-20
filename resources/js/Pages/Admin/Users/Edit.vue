<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    user: Object,
    roles: Object,
});

const primaryRole = () => {
    if (Array.isArray(props.user.role_names) && props.user.role_names.length > 0) {
        return props.user.role_names[0];
    }

    return 'supplier';
};

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: primaryRole(),
    locale: props.user.locale || 'ru',
});

const submit = () => {
    form.put(route('admin.users.update', props.user.id));
};
</script>

<template>
    <AdminLayout>
        <div class="admin-users-edit">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.users.edit_title') }}</h1>
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

                        <div class="col-12">
                            <div class="alert alert-info">
                                <small class="text-muted">
                                    <strong>{{ t('common.info', 'Информация') }}:</strong>
                                    {{ t('admin.users.edit_info',
                                        'Для изменения пароля используйте функцию сброса пароля') }}
                                </small>
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
