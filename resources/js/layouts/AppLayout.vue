<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    visible?: boolean;
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    visible: true,
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" :visible="visible">
        <Toaster
            position="bottom-right"
            richColors
            :expand="true"
        />
        <slot />
    </AppLayout>
</template>
