import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useTranslations() {
    const page = usePage();
    const translations = computed(() => page.props.translations || {});

    const t = (key, fallback = '') => {
        if (!key) {
            return fallback;
        }

        const parts = String(key).split('.');
        let current = translations.value;

        for (const part of parts) {
            if (current && Object.prototype.hasOwnProperty.call(current, part)) {
                current = current[part];
            } else {
                return fallback || key;
            }
        }

        if (typeof current === 'string') {
            return current;
        }

        return fallback || key;
    };

    return { t };
}
