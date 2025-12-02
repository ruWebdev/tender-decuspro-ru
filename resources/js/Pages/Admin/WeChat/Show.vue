<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';
import { ref, nextTick, onMounted } from 'vue';

const { t } = useTranslations();

const props = defineProps({
    conversation: Object,
    messages: Array,
    suppliers: Array,
});

// Форма отправки сообщения
const messageForm = useForm({
    content: '',
});

const messagesContainer = ref(null);

const sendMessage = () => {
    if (!messageForm.content.trim()) return;
    
    messageForm.post(route('admin.wechat.send', props.conversation.id), {
        preserveScroll: true,
        onSuccess: () => {
            messageForm.reset();
            scrollToBottom();
        },
    });
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

onMounted(() => {
    scrollToBottom();
});

// Форма привязки поставщика
const showLinkSupplier = ref(false);
const linkForm = useForm({
    platform_supplier_id: props.conversation.platform_supplier_id || '',
});

const linkSupplier = () => {
    linkForm.post(route('admin.wechat.link', props.conversation.id), {
        preserveScroll: true,
        onSuccess: () => {
            showLinkSupplier.value = false;
        },
    });
};

// Форма заметки
const showRemark = ref(false);
const remarkForm = useForm({
    remark: props.conversation.remark || '',
});

const updateRemark = () => {
    remarkForm.post(route('admin.wechat.remark', props.conversation.id), {
        preserveScroll: true,
        onSuccess: () => {
            showRemark.value = false;
        },
    });
};

// Перевод сообщения
const translateMessage = (messageId) => {
    router.post(route('admin.wechat.translate', messageId), {}, {
        preserveScroll: true,
    });
};

// Форматирование даты
const formatDateTime = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// Удаление диалога
const deleteConversation = () => {
    if (confirm(t('admin.wechat.confirm_delete', 'Удалить этот диалог?'))) {
        router.delete(route('admin.wechat.destroy', props.conversation.id));
    }
};
</script>

<template>
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <Link :href="route('admin.wechat.index')" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </Link>
                <div>
                    <h1 class="h4 mb-0">
                        {{ conversation.remark || conversation.platform_supplier?.name || conversation.nickname || conversation.wechat_openid }}
                    </h1>
                    <small class="text-muted">
                        <span v-if="conversation.is_subscribed" class="text-success">
                            <i class="bi bi-check-circle me-1"></i>{{ t('admin.wechat.subscribed', 'Подписан') }}
                        </span>
                        <span v-else class="text-danger">
                            <i class="bi bi-x-circle me-1"></i>{{ t('admin.wechat.unsubscribed', 'Отписан') }}
                        </span>
                    </small>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <button class="dropdown-item" @click="showLinkSupplier = true">
                            <i class="bi bi-link me-2"></i>{{ t('admin.wechat.link_supplier', 'Привязать поставщика') }}
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item" @click="showRemark = true">
                            <i class="bi bi-pencil me-2"></i>{{ t('admin.wechat.edit_remark', 'Изменить заметку') }}
                        </button>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button class="dropdown-item text-danger" @click="deleteConversation">
                            <i class="bi bi-trash me-2"></i>{{ t('common.delete', 'Удалить') }}
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Модальное окно привязки поставщика -->
        <div v-if="showLinkSupplier" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ t('admin.wechat.link_supplier', 'Привязать поставщика') }}</h5>
                        <button type="button" class="btn-close" @click="showLinkSupplier = false"></button>
                    </div>
                    <form @submit.prevent="linkSupplier">
                        <div class="modal-body">
                            <select v-model="linkForm.platform_supplier_id" class="form-select">
                                <option value="">{{ t('admin.wechat.no_supplier', '— Не привязан —') }}</option>
                                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                    {{ supplier.name }}
                                </option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="showLinkSupplier = false">
                                {{ t('common.cancel', 'Отмена') }}
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="linkForm.processing">
                                {{ t('common.save', 'Сохранить') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Модальное окно заметки -->
        <div v-if="showRemark" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ t('admin.wechat.edit_remark', 'Изменить заметку') }}</h5>
                        <button type="button" class="btn-close" @click="showRemark = false"></button>
                    </div>
                    <form @submit.prevent="updateRemark">
                        <div class="modal-body">
                            <input v-model="remarkForm.remark" type="text" class="form-control"
                                :placeholder="t('admin.wechat.remark_placeholder', 'Заметка для этого контакта')">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="showRemark = false">
                                {{ t('common.cancel', 'Отмена') }}
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="remarkForm.processing">
                                {{ t('common.save', 'Сохранить') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Область сообщений -->
        <div class="card">
            <div ref="messagesContainer" class="card-body messages-container">
                <div v-if="messages.length === 0" class="text-center py-5 text-muted">
                    <i class="bi bi-chat-dots fs-1 d-block mb-3"></i>
                    {{ t('admin.wechat.no_messages', 'Сообщений пока нет') }}
                </div>

                <div v-for="message in messages" :key="message.id"
                    :class="['message', message.direction === 'outgoing' ? 'message-outgoing' : 'message-incoming']">
                    <div class="message-bubble">
                        <div class="message-content">
                            {{ message.content }}
                        </div>
                        <div v-if="message.translated_content_ru && message.direction === 'incoming'" 
                            class="message-translation">
                            <small class="text-muted">
                                <i class="bi bi-translate me-1"></i>{{ message.translated_content_ru }}
                            </small>
                        </div>
                        <div class="message-meta">
                            <small class="text-muted">
                                {{ formatDateTime(message.created_at) }}
                                <span v-if="message.sender">• {{ message.sender.name }}</span>
                            </small>
                            <button v-if="message.direction === 'incoming' && !message.translated_content_ru"
                                class="btn btn-link btn-sm p-0 ms-2" @click="translateMessage(message.id)"
                                :title="t('admin.wechat.translate', 'Перевести')">
                                <i class="bi bi-translate"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Форма отправки -->
            <div class="card-footer">
                <form @submit.prevent="sendMessage" class="d-flex gap-2">
                    <input v-model="messageForm.content" type="text" class="form-control"
                        :placeholder="t('admin.wechat.input_placeholder', 'Введите сообщение...')"
                        :disabled="!conversation.is_subscribed">
                    <button type="submit" class="btn btn-primary" 
                        :disabled="messageForm.processing || !messageForm.content.trim() || !conversation.is_subscribed">
                        <i class="bi bi-send"></i>
                    </button>
                </form>
                <small v-if="!conversation.is_subscribed" class="text-danger mt-2 d-block">
                    {{ t('admin.wechat.cannot_send', 'Пользователь отписался. Отправка сообщений невозможна.') }}
                </small>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.messages-container {
    height: 500px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.message {
    display: flex;
    max-width: 70%;
}

.message-incoming {
    align-self: flex-start;
}

.message-outgoing {
    align-self: flex-end;
}

.message-bubble {
    padding: 10px 14px;
    border-radius: 12px;
    max-width: 100%;
}

.message-incoming .message-bubble {
    background-color: #f1f3f5;
}

.message-outgoing .message-bubble {
    background-color: #0d6efd;
    color: white;
}

.message-outgoing .message-meta {
    color: rgba(255, 255, 255, 0.7);
}

.message-translation {
    margin-top: 6px;
    padding-top: 6px;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.message-meta {
    margin-top: 4px;
    font-size: 0.75rem;
}
</style>
