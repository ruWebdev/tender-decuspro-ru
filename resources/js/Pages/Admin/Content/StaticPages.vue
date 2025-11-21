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
        user_agreement: {
            slug: initial.value.user_agreement?.slug || 'user-agreement',
            title_ru: initial.value.user_agreement?.title_ru || '',
            title_en: initial.value.user_agreement?.title_en || '',
            title_cn: initial.value.user_agreement?.title_cn || '',
            body_ru: initial.value.user_agreement?.body_ru || '',
            body_en: initial.value.user_agreement?.body_en || '',
            body_cn: initial.value.user_agreement?.body_cn || '',
            published: Boolean(initial.value.user_agreement?.published ?? true),
        },
        privacy_policy: {
            slug: initial.value.privacy_policy?.slug || 'privacy-policy',
            title_ru: initial.value.privacy_policy?.title_ru || '',
            title_en: initial.value.privacy_policy?.title_en || '',
            title_cn: initial.value.privacy_policy?.title_cn || '',
            body_ru: initial.value.privacy_policy?.body_ru || '',
            body_en: initial.value.privacy_policy?.body_en || '',
            body_cn: initial.value.privacy_policy?.body_cn || '',
            published: Boolean(initial.value.privacy_policy?.published ?? true),
        },
        procurement_rules: {
            slug: initial.value.procurement_rules?.slug || 'procurement-rules',
            title_ru: initial.value.procurement_rules?.title_ru || '',
            title_en: initial.value.procurement_rules?.title_en || '',
            title_cn: initial.value.procurement_rules?.title_cn || '',
            body_ru: initial.value.procurement_rules?.body_ru || '',
            body_en: initial.value.procurement_rules?.body_en || '',
            body_cn: initial.value.procurement_rules?.body_cn || '',
            published: Boolean(initial.value.procurement_rules?.published ?? true),
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
                        <h2 class="card-title">{{ t('admin.content.static_pages.pages.user_agreement') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_ru')
                                }}</label>
                                <input class="form-control" v-model="form.pages.user_agreement.title_ru">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_en')
                                }}</label>
                                <input class="form-control" v-model="form.pages.user_agreement.title_en">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_cn')
                                }}</label>
                                <input class="form-control" v-model="form.pages.user_agreement.title_cn">
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_ru')
                                }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.user_agreement.body_ru"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_en')
                                }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.user_agreement.body_en"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_cn')
                                }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.user_agreement.body_cn"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        v-model="form.pages.user_agreement.published">
                                    <span class="form-check-label">{{
                                        t('admin.content.static_pages.fields.published') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ t('admin.content.static_pages.pages.privacy_policy') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_ru')
                                }}</label>
                                <input class="form-control" v-model="form.pages.privacy_policy.title_ru">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_en')
                                }}</label>
                                <input class="form-control" v-model="form.pages.privacy_policy.title_en">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_cn')
                                }}</label>
                                <input class="form-control" v-model="form.pages.privacy_policy.title_cn">
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_ru')
                                }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.privacy_policy.body_ru"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_en')
                                }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.privacy_policy.body_en"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_cn')
                                }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.privacy_policy.body_cn"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        v-model="form.pages.privacy_policy.published">
                                    <span class="form-check-label">{{
                                        t('admin.content.static_pages.fields.published') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ t('admin.content.static_pages.pages.procurement_rules') }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_ru')
                                }}</label>
                                <input class="form-control" v-model="form.pages.procurement_rules.title_ru">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_en')
                                }}</label>
                                <input class="form-control" v-model="form.pages.procurement_rules.title_en">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.title_cn')
                                }}</label>
                                <input class="form-control" v-model="form.pages.procurement_rules.title_cn">
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_ru')
                                }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.procurement_rules.body_ru"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_en')
                                }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.procurement_rules.body_en"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.content.static_pages.fields.body_cn')
                                }}</label>
                                <QuillEditor class="mb-3" v-model:content="form.pages.procurement_rules.body_cn"
                                    contentType="html" theme="snow" />
                            </div>
                            <div class="col-12">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                        v-model="form.pages.procurement_rules.published">
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
