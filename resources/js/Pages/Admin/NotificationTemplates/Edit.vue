<script setup>
import { computed } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    template: {
        type: Object,
        default: null,
    },
    types: {
        type: Array,
        default: () => [],
    },
});

const isEdit = computed(() => !!props.template);

const form = useForm({
    name: props.template?.name || '',
    type: props.template?.type || (props.types[0] || ''),
    body_ru: props.template?.body_ru || '',
    body_en: props.template?.body_en || '',
    body_cn: props.template?.body_cn || '',
});

const submit = () => {
    if (isEdit.value && props.template) {
        form.put(route('admin.notification_templates.update', props.template.id));
    } else {
        form.post(route('admin.notification_templates.store'));
    }
};

const typeLabel = (type) => {
    const key = `admin.notification_templates.types.${type}`;
    const translation = t(key);
    return translation === key ? type : translation;
};
</script>

<template>
    <AdminLayout>
        <div class="admin-notification-templates-edit">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    {{ isEdit ? t('admin.notification_templates.edit_title') :
                        t('admin.notification_templates.create_title') }}
                </h1>
                <Link :href="route('admin.notification_templates.index')" class="btn btn-outline-secondary">
                {{ t('common.back', 'Назад') }}
                </Link>
            </div>

            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="submit" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ t('admin.notification_templates.form.name') }}</label>
                                <input v-model="form.name" type="text" class="form-control"
                                    :class="{ 'is-invalid': form.errors.name }" />
                                <div v-if="form.errors.name" class="invalid-feedback">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ t('admin.notification_templates.form.type') }}</label>
                                <select v-model="form.type" class="form-select"
                                    :class="{ 'is-invalid': form.errors.type }">
                                    <option v-for="type in types" :key="type" :value="type">
                                        {{ typeLabel(type) }}
                                    </option>
                                </select>
                                <div v-if="form.errors.type" class="invalid-feedback">
                                    {{ form.errors.type }}
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ t('admin.notification_templates.form.body_ru') }}</label>
                                <textarea v-model="form.body_ru" class="form-control" rows="3"
                                    :class="{ 'is-invalid': form.errors.body_ru }"></textarea>
                                <div v-if="form.errors.body_ru" class="invalid-feedback">
                                    {{ form.errors.body_ru }}
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ t('admin.notification_templates.form.body_en') }}</label>
                                <textarea v-model="form.body_en" class="form-control" rows="3"
                                    :class="{ 'is-invalid': form.errors.body_en }"></textarea>
                                <div v-if="form.errors.body_en" class="invalid-feedback">
                                    {{ form.errors.body_en }}
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ t('admin.notification_templates.form.body_cn') }}</label>
                                <textarea v-model="form.body_cn" class="form-control" rows="3"
                                    :class="{ 'is-invalid': form.errors.body_cn }"></textarea>
                                <div v-if="form.errors.body_cn" class="invalid-feedback">
                                    {{ form.errors.body_cn }}
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
