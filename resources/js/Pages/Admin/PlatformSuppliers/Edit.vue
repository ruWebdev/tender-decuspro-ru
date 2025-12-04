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
    title: props.supplier?.title || '',
    phone: props.supplier?.phone || '',
    all_phones: props.supplier?.all_phones || '',
    email: props.supplier?.email || '',
    all_emails: props.supplier?.all_emails || '',
    website: props.supplier?.website || '',
    location: props.supplier?.location || '',
    contact_person: props.supplier?.contact_person || '',
    established_year: props.supplier?.established_year || '',
    employee_count: props.supplier?.employee_count || '',
    comment: props.supplier?.comment || '',
    description: props.supplier?.description || '',
    main_products: props.supplier?.main_products || '',
    export_markets: props.supplier?.export_markets || '',
    certifications: props.supplier?.certifications || '',
    keyword: props.supplier?.keyword || '',
    language: props.supplier?.language || 'ru',
    invitation_sent: props.supplier?.invitation_sent || false,
});

const languageOptions = [
    { value: 'ru', label: () => t('admin.platform_suppliers.languages.ru') },
    { value: 'en', label: () => t('admin.platform_suppliers.languages.en') },
    { value: 'cn', label: () => t('admin.platform_suppliers.languages.cn') },
];

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
                                <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.name }" />
                                <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.title') }}</label>
                                <input v-model="form.title" type="text" class="form-control" :class="{ 'is-invalid': form.errors.title }" />
                                <div v-if="form.errors.title" class="invalid-feedback">{{ form.errors.title }}</div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.phone') }}</label>
                                <input v-model="form.phone" type="text" class="form-control" :class="{ 'is-invalid': form.errors.phone }" />
                                <div v-if="form.errors.phone" class="invalid-feedback">{{ form.errors.phone }}</div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.email') }}</label>
                                <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" />
                                <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.website') }}</label>
                                <input v-model="form.website" type="text" class="form-control" :class="{ 'is-invalid': form.errors.website }" />
                                <div v-if="form.errors.website" class="invalid-feedback">{{ form.errors.website }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.location') }}</label>
                                <input v-model="form.location" type="text" class="form-control" :class="{ 'is-invalid': form.errors.location }" />
                                <div v-if="form.errors.location" class="invalid-feedback">{{ form.errors.location }}</div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.language') }}</label>
                                <select v-model="form.language" class="form-select" :class="{ 'is-invalid': form.errors.language }">
                                    <option v-for="option in languageOptions" :key="option.value" :value="option.value">{{ option.label() }}</option>
                                </select>
                                <div v-if="form.errors.language" class="invalid-feedback">{{ form.errors.language }}</div>
                            </div>

                            <div class="col-md-3 d-flex align-items-center">
                                <div class="form-check mt-4">
                                    <input id="invitation_sent" v-model="form.invitation_sent" class="form-check-input" type="checkbox">
                                    <label class="form-check-label" for="invitation_sent">{{ t('admin.platform_suppliers.form.invitation_sent') }}</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.contact_person') }}</label>
                                <input v-model="form.contact_person" type="text" class="form-control" :class="{ 'is-invalid': form.errors.contact_person }" />
                                <div v-if="form.errors.contact_person" class="invalid-feedback">{{ form.errors.contact_person }}</div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.established_year') }}</label>
                                <input v-model="form.established_year" type="number" min="0" class="form-control" :class="{ 'is-invalid': form.errors.established_year }" />
                                <div v-if="form.errors.established_year" class="invalid-feedback">{{ form.errors.established_year }}</div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.employee_count') }}</label>
                                <input v-model="form.employee_count" type="number" min="0" class="form-control" :class="{ 'is-invalid': form.errors.employee_count }" />
                                <div v-if="form.errors.employee_count" class="invalid-feedback">{{ form.errors.employee_count }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.comment') }}</label>
                                <textarea v-model="form.comment" class="form-control" rows="4" :class="{ 'is-invalid': form.errors.comment }"></textarea>
                                <div v-if="form.errors.comment" class="invalid-feedback">{{ form.errors.comment }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.description') }}</label>
                                <textarea v-model="form.description" class="form-control" rows="3" :class="{ 'is-invalid': form.errors.description }"></textarea>
                                <div v-if="form.errors.description" class="invalid-feedback">{{ form.errors.description }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.main_products') }}</label>
                                <textarea v-model="form.main_products" class="form-control" rows="2" :class="{ 'is-invalid': form.errors.main_products }"></textarea>
                                <div v-if="form.errors.main_products" class="invalid-feedback">{{ form.errors.main_products }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.export_markets') }}</label>
                                <textarea v-model="form.export_markets" class="form-control" rows="2" :class="{ 'is-invalid': form.errors.export_markets }"></textarea>
                                <div v-if="form.errors.export_markets" class="invalid-feedback">{{ form.errors.export_markets }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.certifications') }}</label>
                                <textarea v-model="form.certifications" class="form-control" rows="2" :class="{ 'is-invalid': form.errors.certifications }"></textarea>
                                <div v-if="form.errors.certifications" class="invalid-feedback">{{ form.errors.certifications }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.all_emails') }}</label>
                                <textarea v-model="form.all_emails" class="form-control" rows="2" :class="{ 'is-invalid': form.errors.all_emails }"></textarea>
                                <div v-if="form.errors.all_emails" class="invalid-feedback">{{ form.errors.all_emails }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.all_phones') }}</label>
                                <textarea v-model="form.all_phones" class="form-control" rows="2" :class="{ 'is-invalid': form.errors.all_phones }"></textarea>
                                <div v-if="form.errors.all_phones" class="invalid-feedback">{{ form.errors.all_phones }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ t('admin.platform_suppliers.form.keyword') }}</label>
                                <input v-model="form.keyword" type="text" class="form-control" :class="{ 'is-invalid': form.errors.keyword }" />
                                <div v-if="form.errors.keyword" class="invalid-feedback">{{ form.errors.keyword }}</div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status" />
                                {{ isEdit ? t('common.save', 'Сохранить') : t('common.create', 'Создать') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
