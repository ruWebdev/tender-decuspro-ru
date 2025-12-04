<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const page = usePage();
const { t } = useTranslations();

const initial = computed(() => page.props.pages || {});

const form = useForm({
    pages: {
        supplier_terms: {
            slug: initial.value.supplier_terms?.slug || 'supplier-terms',
            title_ru: initial.value.supplier_terms?.title_ru || '',
            title_en: initial.value.supplier_terms?.title_en || '',
            title_cn: initial.value.supplier_terms?.title_cn || '',
            body_ru: initial.value.supplier_terms?.body_ru || '',
            body_en: initial.value.supplier_terms?.body_en || '',
            body_cn: initial.value.supplier_terms?.body_cn || '',
            published: Boolean(initial.value.supplier_terms?.published ?? true),
        },
    },
});

const save = () => {
    form.post(route('admin.content.static_pages.update'));
};
</script>

<template>
    <AdminLayout>

        <h1 class="h2 mb-4">{{ t('admin.content.static_pages.title') }}</h1>

        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ t('admin.content.static_pages.pages.supplier_terms') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_ru')
                                    }}</label>
                                <input class="form-control" v-model="form.pages.supplier_terms.title_ru">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_en')
                                    }}</label>
                                <input class="form-control" v-model="form.pages.supplier_terms.title_en">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_cn')
                                    }}</label>
                                <input class="form-control" v-model="form.pages.supplier_terms.title_cn">
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_ru')
                                    }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.supplier_terms.body_ru"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_en')
                                    }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.supplier_terms.body_en"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_cn')
                                    }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.supplier_terms.body_cn"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        v-model="form.pages.supplier_terms.published">
                                    <span class="form-check-label">{{
                                        t('admin.content.static_pages.fields.published') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 text-end">
            <button class="btn btn-primary" :disabled="form.processing" @click="save">
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                {{ t('admin.content.static_pages.actions.save') }}
            </button>
        </div>

    </AdminLayout>
</template>

<style scoped>
:deep(.ql-container) {
    height: auto !important;
}

:deep(.ql-editor) {
    min-height: 240px;
}
</style>
