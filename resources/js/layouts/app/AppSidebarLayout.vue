<script setup lang="ts">
import AppContent from '@/components/core/AppContent.vue';
import AppShell from '@/components/core/AppShell.vue';
import AppSidebar from '@/components/core/AppSidebar.vue';
import AppSidebarHeader from '@/components/core/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
