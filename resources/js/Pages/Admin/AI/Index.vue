<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    settings: Object,
});

const form = useForm({
    deepseek_api_key: props.settings?.deepseek_api_key || '',
    tender_prompt: props.settings?.tender_prompt || '',
});

const saveSettings = () => {
    form.post(route('admin.ai.save_settings'), {
        preserveScroll: true,
    });
};

const generateTender = () => {
    if (!form.deepseek_api_key) {
        alert('Сначала введите API ключ Deepseek');
        return;
    }

    form.post(route('admin.ai.generate_tender'), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Тендер успешно сгенерирован');
        },
        onError: () => {
            alert('Ошибка при генерации тендера');
        }
    });
};

const translateTenders = () => {
    if (!form.deepseek_api_key) {
        alert('Сначала введите API ключ Deepseek');
        return;
    }

    form.post(route('admin.ai.translate_tenders'), {
        preserveScroll: true,
        onSuccess: (page) => {
            if (page.props.message) {
                alert(page.props.message);
            } else {
                alert('Перевод тендеров запущен');
            }
        },
        onError: () => {
            alert('Ошибка при переводе тендеров');
        }
    });
};
</script>

<template>
    <AdminLayout>
        <div class="admin-ai">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.ai.title') }}</h1>
            </div>

            <!-- Настройки ИИ -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ t('admin.ai.settings.title') }}</h5>
                </div>
                <div class="card-body">
                    <form @submit.prevent="saveSettings">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ t('admin.ai.settings.deepseek_api_key') }}</label>
                                <input type="password" v-model="form.deepseek_api_key" class="form-control"
                                    :class="{ 'is-invalid': form.errors.deepseek_api_key }" placeholder="sk-...">
                                <div v-if="form.errors.deepseek_api_key" class="invalid-feedback">
                                    {{ form.errors.deepseek_api_key }}
                                </div>
                                <div class="form-text">{{ t('admin.ai.settings.deepseek_api_key_note') }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ t('admin.ai.settings.tender_prompt') }}</label>
                                <textarea v-model="form.tender_prompt" class="form-control" rows="12"
                                    :class="{ 'is-invalid': form.errors.tender_prompt }"
                                    placeholder="Введите промпт..."></textarea>
                                <div v-if="form.errors.tender_prompt" class="invalid-feedback">
                                    {{ form.errors.tender_prompt }}
                                </div>
                                <div class="form-text">{{ t('admin.ai.settings.tender_prompt_note') }}</div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ t('admin.ai.settings.save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Инструменты ИИ -->
            <div class="row g-4">
                <!-- Генерация тендера -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div
                                    class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="ti ti-writing"></i>
                                </div>
                                <h5 class="card-title mb-0">{{ t('admin.ai.tender_generation') }}</h5>
                            </div>
                            <p class="text-muted mb-3">{{ t('admin.ai.tender_generation_desc') }}</p>
                            <button @click="generateTender" class="btn btn-primary w-100" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                {{ t('admin.ai.actions.generate') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Перевод -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div
                                    class="avatar avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="ti ti-language"></i>
                                </div>
                                <h5 class="card-title mb-0">{{ t('admin.ai.translation') }}</h5>
                            </div>
                            <p class="text-muted mb-3">{{ t('admin.ai.translation_desc') }}</p>
                            <button @click="translateTenders" class="btn btn-success w-100" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                {{ t('admin.ai.actions.translate') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.avatar {
    width: 40px;
    height: 40px;
    font-size: 18px;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
