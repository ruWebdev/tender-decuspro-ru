<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';
import { ref, computed } from 'vue';

const { t } = useTranslations();

const props = defineProps({
    conversations: Object,
    filters: Object,
    settings: Object,
    isConfigured: Boolean,
    suppliers: Array,
    webhookUrl: String,
});

// Фильтры
const searchQuery = ref(props.filters?.search || '');
const unreadOnly = ref(props.filters?.unread_only || false);

const applyFilters = () => {
    router.get(route('admin.wechat.index'), {
        search: searchQuery.value || undefined,
        unread_only: unreadOnly.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

// Форма настроек
const showSettings = ref(false);
const settingsForm = useForm({
    app_id: props.settings?.app_id || '',
    app_secret: '',
    token: props.settings?.token || '',
    encoding_aes_key: props.settings?.encoding_aes_key || '',
    is_active: props.settings?.is_active || false,
});

const saveSettings = () => {
    settingsForm.post(route('admin.wechat.settings.save'), {
        preserveScroll: true,
        onSuccess: () => {
            showSettings.value = false;
        },
    });
};

const testConnection = () => {
    router.post(route('admin.wechat.test'), {}, {
        preserveScroll: true,
    });
};

// Форматирование даты
const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const now = new Date();
    const diff = now - date;
    
    // Если меньше суток — показываем время
    if (diff < 86400000) {
        return date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
    }
    
    // Иначе — дату
    return date.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit' });
};

// Сокращение текста
const truncate = (text, length = 50) => {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
};

// Копирование URL
const copyWebhookUrl = () => {
    navigator.clipboard.writeText(props.webhookUrl);
};
</script>

<template>
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ t('admin.wechat.title', 'WeChat') }}</h1>
            <button class="btn btn-outline-secondary" @click="showSettings = !showSettings">
                <i class="bi bi-gear me-1"></i>
                {{ t('admin.wechat.settings.title', 'Настройки') }}
            </button>
        </div>

        <!-- Уведомление о статусе -->
        <div v-if="!isConfigured" class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ t('admin.wechat.not_configured', 'WeChat не настроен. Укажите AppID и AppSecret в настройках.') }}
        </div>

        <!-- Панель настроек -->
        <div v-if="showSettings" class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ t('admin.wechat.settings.title', 'Настройки WeChat') }}</h5>
            </div>
            <div class="card-body">
                <form @submit.prevent="saveSettings">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.wechat.settings.app_id', 'AppID') }}</label>
                            <input v-model="settingsForm.app_id" type="text" class="form-control"
                                :class="{ 'is-invalid': settingsForm.errors.app_id }"
                                placeholder="wx1234567890abcdef">
                            <div v-if="settingsForm.errors.app_id" class="invalid-feedback">
                                {{ settingsForm.errors.app_id }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.wechat.settings.app_secret', 'AppSecret') }}</label>
                            <input v-model="settingsForm.app_secret" type="password" class="form-control"
                                :placeholder="settings?.has_secret ? '••••••••' : ''">
                            <small class="text-muted">
                                {{ t('admin.wechat.settings.app_secret_hint', 'Оставьте пустым, чтобы сохранить текущий') }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.wechat.settings.token', 'Token') }}</label>
                            <input v-model="settingsForm.token" type="text" class="form-control"
                                placeholder="your_token">
                            <small class="text-muted">
                                {{ t('admin.wechat.settings.token_hint', 'Токен для верификации сервера') }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ t('admin.wechat.settings.encoding_key', 'EncodingAESKey') }}</label>
                            <input v-model="settingsForm.encoding_aes_key" type="text" class="form-control">
                            <small class="text-muted">
                                {{ t('admin.wechat.settings.encoding_key_hint', 'Ключ шифрования (опционально)') }}
                            </small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ t('admin.wechat.settings.webhook_url', 'Webhook URL') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" :value="webhookUrl" readonly>
                                <button type="button" class="btn btn-outline-secondary" @click="copyWebhookUrl">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                {{ t('admin.wechat.settings.webhook_hint', 'Укажите этот URL в настройках WeChat Official Account') }}
                            </small>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input v-model="settingsForm.is_active" type="checkbox" class="form-check-input"
                                    id="is_active">
                                <label class="form-check-label" for="is_active">
                                    {{ t('admin.wechat.settings.is_active', 'Интеграция активна') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2" :disabled="settingsForm.processing">
                                <span v-if="settingsForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                                {{ t('common.save', 'Сохранить') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary" @click="testConnection"
                                :disabled="!settings?.app_id">
                                {{ t('admin.wechat.settings.test', 'Проверить подключение') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Фильтры -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">{{ t('admin.wechat.filters.search', 'Поиск') }}</label>
                        <input v-model="searchQuery" type="text" class="form-control"
                            :placeholder="t('admin.wechat.filters.search_placeholder', 'Имя или заметка...')"
                            @keyup.enter="applyFilters">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input v-model="unreadOnly" type="checkbox" class="form-check-input" id="unread_only"
                                @change="applyFilters">
                            <label class="form-check-label" for="unread_only">
                                {{ t('admin.wechat.filters.unread_only', 'Только непрочитанные') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-primary" @click="applyFilters">
                            {{ t('common.apply', 'Применить') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Список диалогов -->
        <div class="card">
            <div class="card-body p-0">
                <div v-if="conversations.data.length === 0" class="text-center py-5 text-muted">
                    <i class="bi bi-chat-dots fs-1 d-block mb-3"></i>
                    {{ t('admin.wechat.empty', 'Диалогов пока нет') }}
                </div>

                <div v-else class="list-group list-group-flush">
                    <Link v-for="conv in conversations.data" :key="conv.id"
                        :href="route('admin.wechat.show', conv.id)"
                        class="list-group-item list-group-item-action d-flex align-items-center py-3">
                        
                        <!-- Аватар -->
                        <div class="flex-shrink-0 me-3">
                            <img v-if="conv.avatar_url" :src="conv.avatar_url" 
                                class="rounded-circle" width="48" height="48" alt="">
                            <div v-else class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-person text-white"></i>
                            </div>
                        </div>

                        <!-- Информация -->
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-truncate">
                                    {{ conv.remark || conv.platform_supplier?.name || conv.nickname || conv.wechat_openid }}
                                </h6>
                                <small class="text-muted ms-2 flex-shrink-0">
                                    {{ formatDate(conv.last_message?.created_at) }}
                                </small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <p class="mb-0 text-muted small text-truncate">
                                    <span v-if="conv.last_message?.direction === 'outgoing'" class="text-primary">
                                        {{ t('admin.wechat.you', 'Вы') }}:
                                    </span>
                                    {{ truncate(conv.last_message?.content) || t('admin.wechat.no_messages', 'Нет сообщений') }}
                                </p>
                                <span v-if="conv.unread_count > 0" class="badge bg-danger ms-2 flex-shrink-0">
                                    {{ conv.unread_count }}
                                </span>
                            </div>
                            <div v-if="conv.platform_supplier" class="mt-1">
                                <span class="badge bg-info">{{ conv.platform_supplier.name }}</span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Пагинация -->
            <div v-if="conversations.last_page > 1" class="card-footer">
                <nav>
                    <ul class="pagination pagination-sm mb-0 justify-content-center">
                        <li v-for="link in conversations.links" :key="link.label" 
                            :class="['page-item', { active: link.active, disabled: !link.url }]">
                            <Link v-if="link.url" :href="link.url" class="page-link" v-html="link.label" />
                            <span v-else class="page-link" v-html="link.label" />
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.min-width-0 {
    min-width: 0;
}
</style>
