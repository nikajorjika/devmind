<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import 'vue-sonner/style.css';
import { useInertiaToasts } from '@/composables/useInertiaToasts';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

useInertiaToasts();

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
        <Toaster />
    </AppLayout>
</template>
