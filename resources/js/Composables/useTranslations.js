import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useTranslations() {
    const page = usePage();
    const translations = computed(() => page.props.translations || {});
    const overrides = computed(() => page.props.ui_overrides || {});

    const t = (key, fallback = '') => {
        if (!key) {
            return fallback;
        }

        const k = String(key);

        // flat overrides first
        if (overrides.value && Object.prototype.hasOwnProperty.call(overrides.value, k)) {
            const ov = overrides.value[k];
            if (typeof ov === 'string' && ov.length > 0) {
                return ov;
            }
        }

        const parts = k.split('.');
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

        return fallback || k;
    };

    return { t };
}
