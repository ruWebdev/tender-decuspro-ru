<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();

const tender = computed(() => page.props.tender);
const proposal = computed(() => page.props.proposal);
const winner = computed(() => page.props.winner ?? null);

const buildItemsFromTender = () => {
    const tenderItems = tender.value?.items ?? [];
    const proposalItems = proposal.value?.items ?? [];

    return tenderItems.map((tenderItem) => {
        const existing = proposalItems.find(
            (pi) => pi.tender_item_id === tenderItem.id,
        ) || {};

        return {
            tender_item_id: tenderItem.id,
            price: existing.price ?? '',
            comment: existing.comment ?? '',
            title: tenderItem.title ?? '',
            quantity: tenderItem.quantity ?? '',
            unit: tenderItem.unit ?? '',
        };
    });
};

const form = useForm({
    items: buildItemsFromTender(),
});

const isExpired = computed(() => {
    if (!tender.value?.valid_until) {
        return false;
    }

    const now = new Date();
    const until = new Date(tender.value.valid_until);

    return until.getTime() <= now.getTime();
});

const isSubmitted = computed(() => proposal.value?.status === 'submitted');

const isFinished = computed(() => Boolean(tender.value?.is_finished));

const isFormDisabled = computed(() => isExpired.value || isSubmitted.value || isFinished.value);

const canSaveDraft = computed(() => !isExpired.value && !isSubmitted.value && !isFinished.value);
const canSubmit = computed(() => !isExpired.value && !isSubmitted.value && !isFinished.value);

const saveDraft = () => {
    if (!proposal.value) {
        return;
    }

    form.put(route('proposals.update', { proposal: proposal.value.id }));
};

const submitProposal = () => {
    if (!tender.value) {
        return;
    }

    form.post(route('proposals.store', { tender: tender.value.id }));
};
</script>

<template>
    <AppLayout>
        <div class="container mb-4">
            <h1 class="h2 mb-3">Участие в закупке</h1>

            <div v-if="tender">
                <p><strong>Закупка:</strong> {{ tender.title }}</p>
                <p><strong>Срок подачи предложений до:</strong> {{ tender.valid_until }}</p>
            </div>

            <div v-if="proposal">
                <p><strong>Статус предложения:</strong> {{ proposal.status }}</p>
            </div>

            <div v-if="tender?.is_finished" class="mb-3">
                <div v-if="winner && winner.id === proposal?.id">
                    <strong>🎉 Вы победили!</strong>
                </div>
                <div v-else>
                    <strong>⚠️ Тендер завершён. Ваше предложение проиграло.</strong>
                </div>
            </div>

            <div v-if="isExpired" class="mb-3">
                <strong>Срок подачи предложений завершён</strong>
            </div>

            <form @submit.prevent class="card">
                <div class="card-body">
                    <div class="table-responsive mb-3">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Позиция</th>
                                    <th>Количество</th>
                                    <th>Ед. изм.</th>
                                    <th>Цена</th>
                                    <th>Комментарий</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in form.items" :key="item.tender_item_id">
                                    <td>{{ item.title }}</td>
                                    <td><span class="badge bg-blue text-light">{{ item.quantity }}</span></td>
                                    <td>{{ item.unit }}</td>
                                    <td>
                                        <input type="number" step="0.01" min="0" v-model="item.price"
                                            class="form-control form-control-sm"
                                            :class="{ 'is-invalid': form.errors[`items.${index}.price`] }"
                                            :disabled="isFormDisabled">
                                        <div v-if="form.errors[`items.${index}.price`]" class="invalid-feedback">
                                            {{ form.errors[`items.${index}.price`] }}
                                        </div>
                                    </td>
                                    <td>
                                        <textarea rows="2" v-model="item.comment" class="form-control form-control-sm"
                                            :class="{ 'is-invalid': form.errors[`items.${index}.comment`] }"
                                            :disabled="isFormDisabled"></textarea>
                                        <div v-if="form.errors[`items.${index}.comment`]" class="invalid-feedback">
                                            {{ form.errors[`items.${index}.comment`] }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="button" @click="saveDraft" class="btn btn-secondary me-2"
                        :disabled="isFormDisabled || form.processing || !canSaveDraft">
                        Сохранить как черновик
                    </button>

                    <button type="button" @click="submitProposal" class="btn btn-primary"
                        :disabled="isFormDisabled || form.processing || !canSubmit">
                        Отправить предложение
                    </button>
                </div>
            </form>

            <div v-if="tender?.is_finished && winner" class="mt-4 card">
                <div class="card-body">
                    <h2 class="h4 mb-3">Результаты тендера</h2>
                    <p class="mb-3"><strong>Финальная цена победителя:</strong> <span
                            class="badge bg-success text-light">{{ winner.total?.toFixed(2) ?? '-' }}</span></p>

                    <div v-if="winner.items && winner.items.length" class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Позиция</th>
                                    <th>Цена победителя</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in winner.items" :key="item.tender_item_id">
                                    <td>
                                        {{form.items.find((i) => i.tender_item_id === item.tender_item_id)?.title
                                            ?? 'Позиция'}}
                                    </td>
                                    <td><strong>{{ Number(item.price).toFixed(2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
