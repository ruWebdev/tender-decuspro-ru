<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    settings: Object,
});

const form = useForm({
    mail_host: props.settings?.mail_host || '',
    mail_port: props.settings?.mail_port || 587,
    mail_username: props.settings?.mail_username || '',
    mail_password: props.settings?.mail_password || '',
    mail_encryption: props.settings?.mail_encryption ?? 'tls',
    mail_from_address: props.settings?.mail_from_address || '',
    mail_from_name: props.settings?.mail_from_name || '',
});

const save = () => {
    form.post(route('admin.smtp.save'), {
        preserveScroll: true,
    });
};

const testForm = useForm({
    email: '',
});

const sendTest = () => {
    if (!testForm.email) {
        return;
    }

    testForm.post(route('admin.smtp.test'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ t('admin.smtp.title') }}</h1>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form @submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.smtp.fields.host') }}</label>
                            <input v-model="form.mail_host" type="text" class="form-control"
                                :class="{ 'is-invalid': form.errors.mail_host }" placeholder="smtp.example.com">
                            <div v-if="form.errors.mail_host" class="invalid-feedback">{{ form.errors.mail_host }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ t('admin.smtp.fields.port') }}</label>
                            <input v-model.number="form.mail_port" type="number" class="form-control"
                                :class="{ 'is-invalid': form.errors.mail_port }" placeholder="587">
                            <div v-if="form.errors.mail_port" class="invalid-feedback">{{ form.errors.mail_port }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ t('admin.smtp.fields.encryption') }}</label>
                            <select v-model="form.mail_encryption" class="form-select">
                                <option value="tls">{{ t('admin.smtp.fields.encryption_tls') }}</option>
                                <option value="ssl">{{ t('admin.smtp.fields.encryption_ssl') }}</option>
                                <option value="null">{{ t('admin.smtp.fields.encryption_none') }}</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.smtp.fields.username') }}</label>
                            <input v-model="form.mail_username" type="text" class="form-control"
                                placeholder="user@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.smtp.fields.password') }}</label>
                            <input v-model="form.mail_password" type="password" class="form-control"
                                placeholder="••••••••">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.smtp.fields.from_address') }}</label>
                            <input v-model="form.mail_from_address" type="email" class="form-control"
                                :class="{ 'is-invalid': form.errors.mail_from_address }"
                                placeholder="noreply@example.com">
                            <div v-if="form.errors.mail_from_address" class="invalid-feedback">{{
                                form.errors.mail_from_address }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.smtp.fields.from_name') }}</label>
                            <input v-model="form.mail_from_name" type="text" class="form-control"
                                :class="{ 'is-invalid': form.errors.mail_from_name }" placeholder="DecusPro">
                            <div v-if="form.errors.mail_from_name" class="invalid-feedback">{{
                                form.errors.mail_from_name }}</div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                {{ t('common.save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ t('admin.smtp.test.title') }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    {{ t('admin.smtp.test.description') }}
                </p>
                <form @submit.prevent="sendTest">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.smtp.test.email') }}</label>
                            <input v-model="testForm.email" type="email" class="form-control"
                                :class="{ 'is-invalid': testForm.errors.email }"
                                :placeholder="t('admin.smtp.test.email_placeholder')">
                            <div v-if="testForm.errors.email" class="invalid-feedback">
                                {{ testForm.errors.email }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-outline-primary w-100"
                                :disabled="testForm.processing || !testForm.email">
                                <span v-if="testForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                                {{ t('admin.smtp.test.button') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
