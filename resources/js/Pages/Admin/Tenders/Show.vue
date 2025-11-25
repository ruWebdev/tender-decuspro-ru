<script setup>
import { computed, ref } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const page = usePage();
const { t } = useTranslations();

const props = defineProps({
    tender: Object,
    chats: {
        type: Array,
        default: () => [],
    },
    hasUnreadChatMessages: {
        type: Boolean,
        default: false,
    },
});

const canRetender = computed(() => {
    if (!props.tender) return false;
    return Boolean(props.tender.is_finished) || props.tender.status === 'closed';
});

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    return date.toLocaleDateString(page.props.locale || 'ru', {
        day: '2-digit',
        'month': '2-digit',
        year: 'numeric',
    });
};

const statusLabel = (status) => {
    const statuses = {
        'open': t('admin.tenders.statuses.open'),
        'closed': t('admin.tenders.statuses.closed'),
        'review': t('admin.tenders.statuses.review'),
    };
    return statuses[status] || status;
};

const statusBadgeClass = (status) => {
    switch (status) {
        case 'open':
            return 'bg-success';
        case 'closed':
            return 'bg-secondary';
        case 'review':
            return 'bg-warning';
        default:
            return 'bg-dark';
    }
};

const finishedBadgeClass = (isFinished) => {
    return isFinished ? 'bg-info' : 'bg-primary';
};

const finishedLabel = (isFinished) => {
    return isFinished ? t('admin.tenders.filters.finished') : t('admin.tenders.filters.active');
};

const chatList = computed(() => props.chats || []);
const hasUnreadChat = computed(() => !!props.hasUnreadChatMessages);

const isChatOpen = ref(false);
const selectedChatId = ref(null);

const currentChat = computed(() => {
    if (!chatList.value.length) {
        return null;
    }

    const found = chatList.value.find((chat) => chat.id === selectedChatId.value);

    return found || chatList.value[0];
});

const markChatAsRead = () => {
    if (!currentChat.value) {
        return;
    }

    router.post(route('admin.tenders.chats.read', {
        tender: props.tender.id,
        chat: currentChat.value.id,
    }), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            isChatOpen.value = true;
        },
    });
};

const openChat = () => {
    if (!chatList.value.length) {
        return;
    }

    if (!selectedChatId.value && chatList.value[0]) {
        selectedChatId.value = chatList.value[0].id;
    }

    if (isChatOpen.value) {
        return;
    }

    markChatAsRead();
};

const closeChat = () => {
    isChatOpen.value = false;
};

const selectChat = (chatId) => {
    selectedChatId.value = chatId;

    if (!isChatOpen.value) {
        isChatOpen.value = true;
    }

    markChatAsRead();
};

const chatForm = useForm({
    body: '',
});

const sendChatMessage = () => {
    if (!currentChat.value) return;
    if (!chatForm.body || !chatForm.body.trim()) return;

    chatForm.post(route('admin.tenders.chats.messages.store', {
        tender: props.tender.id,
        chat: currentChat.value.id,
    }), {
        preserveScroll: true,
        onSuccess: () => {
            chatForm.reset('body');
        },
    });
};

const toggleTranslate = () => {
    if (!currentChat.value) {
        return;
    }

    router.post(route('admin.tenders.chats.toggle_translate', {
        tender: props.tender.id,
        chat: currentChat.value.id,
    }), {}, {
        preserveScroll: true,
    });
};

const retender = () => {
    if (!props.tender) return;
    if (!confirm(t('admin.tenders.actions.confirm_retender', 'Создать переторжку на основе этого тендера?'))) {
        return;
    }

    router.post(route('admin.tenders.retender', props.tender.id));
};
</script>

<template>
    <AdminLayout>
        <div class="admin-tenders-show">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ t('admin.tenders.show_title') }}</h1>
                <div class="d-flex gap-2">
                    <button v-if="chatList.length" type="button" class="btn btn-outline-info position-relative"
                        :class="{ 'btn-chat-blink': hasUnreadChat }" @click="openChat">
                        <i class="ti ti-message-circle me-1"></i>
                        {{ t('admin.tenders.chat.button_title') }}
                        <span v-if="hasUnreadChat"
                            class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    </button>
                    <button v-if="canRetender" type="button" class="btn btn-outline-primary" @click="retender">
                        {{ t('admin.tenders.actions.retender', 'Объявить переторжку') }}
                    </button>
                    <Link :href="route('admin.tenders.edit', tender.id)" class="btn btn-warning">
                    {{ t('admin.tenders.actions.edit') }}
                    </Link>
                    <Link :href="route('admin.tenders.index')" class="btn btn-outline-secondary">
                    {{ t('common.back', 'Назад') }}
                    </Link>
                </div>
            </div>

            <!-- Основная информация -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="m-0">{{ t('common.information', 'Информация') }}</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.form.customer') }}:</strong>
                                {{ tender.customer?.name }} ({{ tender.customer?.email }})
                            </p>
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.form.title') }}:</strong>
                                {{ tender.title }}
                            </p>
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.form.status') }}:</strong>
                                <span class="badge ms-1 me-1 text-light" :class="statusBadgeClass(tender.status)">
                                    {{ statusLabel(tender.status) }}
                                </span>
                                <span class="badge text-light" :class="finishedBadgeClass(tender.is_finished)">
                                    {{ finishedLabel(tender.is_finished) }}
                                </span>
                            </p>
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.form.valid_until') }}:</strong>
                                {{ formatDate(tender.valid_until) }}
                            </p>
                            <p v-if="tender.round_number" class="mb-2">
                                <strong>{{ t('admin.tenders.round_label', 'Раунд торгов') }}:</strong>
                                {{ tender.round_number }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.table.col_created_at') }}:</strong>
                                {{ formatDate(tender.created_at) }}
                            </p>
                            <p v-if="tender.finished_at" class="mb-2">
                                <strong>{{ t('tenders.field_finished_at', 'Завершен') }}:</strong>
                                {{ formatDate(tender.finished_at) }}
                            </p>
                            <p class="mb-2">
                                <strong>{{ t('admin.tenders.table.col_items') }}:</strong>
                                {{ tender.items?.length || 0 }}
                            </p>
                        </div>
                    </div>

                    <div v-if="tender.description" class="mt-3">
                        <strong>{{ t('admin.tenders.form.description') }}:</strong>
                        <p class="mt-1">{{ tender.description }}</p>
                    </div>

                    <div v-if="tender.hidden_comment" class="mt-3">
                        <strong>{{ t('admin.tenders.form.hidden_comment') }}:</strong>
                        <p class="mt-1 text-muted">{{ tender.hidden_comment }}</p>
                    </div>
                </div>
            </div>

            <!-- Позиции тендера -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="m-0">{{ t('tenders.positions_title', 'Позиции тендера') }}</h2>
                </div>
                <div class="table-responsive">
                    <div v-if="!tender.items || tender.items.length === 0" class="text-center text-muted py-4">
                        {{ t('admin.tenders.no_items', 'Нет позиций') }}
                    </div>
                    <table v-else class="table card-table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ t('tenders.col_item_title') }}</th>
                                <th>{{ t('tenders.col_quantity') }}</th>
                                <th>{{ t('tenders.col_unit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in tender.items" :key="item.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.title }}</td>
                                <td>{{ item.quantity }}</td>
                                <td>{{ item.unit || '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <!-- Кнопки действий -->
            <div class="card-footer text-end">
                <Link :href="route('admin.tenders.index')" class="btn btn-outline-secondary me-2">
                {{ t('common.back', 'Назад') }}
                </Link>
                <Link :href="route('admin.tenders.edit', tender.id)" class="btn btn-primary">
                {{ t('admin.tenders.actions.edit') }}
                </Link>
            </div>
        </div>

        <!-- Offcanvas с чатами по тендеру -->
        <div v-if="isChatOpen" class="offcanvas-backdrop-custom" @click="closeChat">
            <div class="offcanvas-panel" @click.stop>
                <div class="offcanvas-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ t('admin.tenders.chat.offcanvas_title') }}</h5>
                    <button type="button" class="btn-close" aria-label="Close" @click="closeChat"></button>
                </div>

                <div class="offcanvas-body">
                    <div class="row g-3">
                        <div class="col-4 border-end">
                            <h6 class="mb-2">{{ t('admin.tenders.chat.list_title') }}</h6>
                            <div v-if="!chatList.length" class="text-muted small">
                                {{ t('admin.tenders.chat.empty') }}
                            </div>
                            <ul v-else class="list-group list-group-flush small">
                                <li v-for="chat in chatList" :key="chat.id"
                                    class="list-group-item list-group-item-action"
                                    :class="{ 'active': currentChat && chat.id === currentChat.id }"
                                    style="cursor: pointer;" @click="selectChat(chat.id)">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">{{ chat.supplier?.name || '-' }}</div>
                                            <div class="text-muted">{{ chat.supplier?.email }}</div>
                                        </div>
                                        <span v-if="chat.unread_count" class="badge bg-danger">
                                            {{ chat.unread_count }}
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="col-8">
                            <h6 class="mb-2">{{ t('admin.tenders.chat.messages_title') }}</h6>
                            <div v-if="!currentChat" class="text-muted small">
                                {{ t('admin.tenders.chat.empty') }}
                            </div>

                            <div v-else>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                        :checked="currentChat.translate_to_ru" @change="toggleTranslate">
                                    <label class="form-check-label">
                                        {{ t('admin.tenders.chat.translate_to_ru') }}
                                    </label>
                                </div>

                                <div class="chat-messages-admin mb-3">
                                    <div v-for="message in currentChat.messages" :key="message.id"
                                        class="chat-message-admin mb-2">
                                        <div class="small fw-semibold mb-1">
                                            <span v-if="message.sender_id === currentChat.supplier.id">
                                                {{ t('admin.tenders.chat.supplier_label') }}
                                            </span>
                                            <span v-else>
                                                {{ t('admin.tenders.chat.customer_label') }}
                                            </span>
                                        </div>
                                        <div class="chat-message-body-admin">
                                            {{ currentChat.translate_to_ru && message.translated_body_ru
                                                ? message.translated_body_ru
                                                : message.body }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <textarea v-model="chatForm.body" class="form-control mb-2" rows="3"
                                        :placeholder="t('admin.tenders.chat.input_placeholder')"
                                        :disabled="chatForm.processing"></textarea>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary btn-sm"
                                            :disabled="chatForm.processing || !chatForm.body || !chatForm.body.trim()"
                                            @click="sendChatMessage">
                                            <span v-if="chatForm.processing"
                                                class="spinner-border spinner-border-sm me-2"></span>
                                            {{ t('admin.tenders.chat.send') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.offcanvas-backdrop-custom {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: flex-end;
    z-index: 1050;
}

.offcanvas-panel {
    width: 50%;
    max-width: 720px;
    background: #fff;
    display: flex;
    flex-direction: column;
}

.offcanvas-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e5e7eb;
}

.offcanvas-body {
    padding: 1rem 1.25rem;
    overflow-y: auto;
    max-height: calc(100vh - 7rem);
}

.chat-messages-admin {
    max-height: 360px;
    overflow-y: auto;
}

.chat-message-admin {
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    background-color: #f5f5f5;
}

.chat-message-body-admin {
    white-space: pre-wrap;
}

.btn-chat-blink {
    animation: chat-blink 1.2s ease-in-out infinite;
}

@keyframes chat-blink {

    0%,
    100% {
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.6);
    }

    50% {
        box-shadow: 0 0 0 0.5rem rgba(13, 110, 253, 0);
    }
}
</style>
