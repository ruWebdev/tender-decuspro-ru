<script setup>
import { computed } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    supplier: {
        type: Object,
        default: null,
    },
});

const isEdit = computed(() => !!props.supplier);

const form = useForm({
    name: props.supplier?.name || '',
    phone: props.supplier?.phone || '',
    email: props.supplier?.email || '',
    website: props.supplier?.website || '',
    comment: props.supplier?.comment || '',
    language: props.supplier?.language || '',
    invitation_sent: props.supplier?.invitation_sent || false,
});

const submit = () => {
    if (isEdit.value && props.supplier) {
        form.put(route('admin.platform_suppliers.update', props.supplier.id));
    } else {
        form.post(route('admin.platform_suppliers.store'));
    }
};
</script>

<template>
    <AdminLayout>
        <div class="admin-platform-suppliers-edit">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    {{ isEdit ? t('admin.platform_suppliers.edit_title') : t('admin.platform_suppliers.create_title') }}
                </h1>
                <Link :href="route('admin.platform_suppliers.index')" class="btn btn-outline-secondary">
                {{ t('common.back', 'Назад') }}
                </Link>
            </div>

            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="submit" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.name') }}</label>
                                <input v-model="form.name" type="text" class="form-control"
                                    :class="{ 'is-invalid': form.errors.name }" />
                                <div v-if="form.errors.name" class="invalid-feedback">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.phone') }}</label>
                                <input v-model="form.phone" type="text" class="form-control"
                                    :class="{ 'is-invalid': form.errors.phone }" />
                                <div v-if="form.errors.phone" class="invalid-feedback">
                                    {{ form.errors.phone }}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.email') }}</label>
                                <input v-model="form.email" type="email" class="form-control"
                                    :class="{ 'is-invalid': form.errors.email }" />
                                <div v-if="form.errors.email" class="invalid-feedback">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.website') }}</label>
                                <input v-model="form.website" type="text" class="form-control"
                                    :class="{ 'is-invalid': form.errors.website }" />
                                <div v-if="form.errors.website" class="invalid-feedback">
                                    {{ form.errors.website }}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.language') }}</label>
                                <input v-model="form.language" type="text" class="form-control"
                                    :class="{ 'is-invalid': form.errors.language }" />
                                <div v-if="form.errors.language" class="invalid-feedback">
                                    {{ form.errors.language }}
                                </div>
                            </div>

                            <div class="col-md-3 d-flex align-items-center">
                                <div class="form-check mt-4">
                                    <input id="invitation_sent" v-model="form.invitation_sent" class="form-check-input"
                                        type="checkbox">
                                    <label class="form-check-label" for="invitation_sent">
                                        {{ t('admin.platform_suppliers.form.invitation_sent') }}
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.comment') }}</label>
                                <textarea v-model="form.comment" class="form-control" rows="4"
                                    :class="{ 'is-invalid': form.errors.comment }"></textarea>
                                <div v-if="form.errors.comment" class="invalid-feedback">
                                    {{ form.errors.comment }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"
                                    role="status" />
                                {{ isEdit ? t('common.save', 'Сохранить') : t('common.create', 'Создать') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
