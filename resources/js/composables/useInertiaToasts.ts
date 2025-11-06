// composables/useInertiaToasts.ts
import { ToastStatus } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { toast } from 'vue-sonner';

export function useInertiaToasts() {
    const page = usePage();
    const show = (
        status: ToastStatus,
        title?: string,
        description?: string,
    ) => {
        if (!title) return;
        toast[status](title, { description });
    };

    watch(
        () => page.props.flash?.id,
        (newId) => {
            if (newId) {
                const { status, title, description } = page.props.flash;
                show(status as ToastStatus, title, description);
            }
        },
    );
}
