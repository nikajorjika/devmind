// composables/useInertiaToasts.ts
import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

type Kind = 'success' | 'error' | 'info' | 'warning';

export function useInertiaToasts() {
    const show = (type: Kind, title?: string, description?: string) => {
        if (!title) return;
        toast[type](title, { description });
    };

    router.on('success', (e: any) => {
        const f = e.detail?.page?.props?.flash;

        if (f?.title) show(f.status as Kind, f.title, f.description);
    });
}
