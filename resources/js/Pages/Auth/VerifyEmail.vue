<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    status: {
        type: String,
    },
});

const { t } = useTranslations();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>

        <Head :title="t('auth.verify_title')" />

        <div class="mb-4 text-sm text-gray-600">
            {{ t('auth.verify_description') }}
        </div>

        <div class="mb-4 text-sm font-medium text-green-600" v-if="verificationLinkSent">
            {{ t('auth.verify_link_sent') }}
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ t('auth.verify_resend_button') }}
                </PrimaryButton>

                <Link :href="route('logout')" method="post" as="button"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ t('auth.verify_logout') }}</Link>
            </div>
        </form>
    </GuestLayout>
</template>
