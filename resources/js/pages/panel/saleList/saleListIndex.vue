<template>
    <Head title="Productos"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-1">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <SaleListTable :sales-list="salesList" :pagination-sale="paginationSale" :loading="loadingSale" @page-change="handlePageChange" />
            </div>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import { useSale } from '@/composables/UseSale';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import SaleListTable from './components/saleListTable.vue';

const { salesList, saleList, paginationSale, loadingSale } = useSale();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'ventas',
        href: '/panel/sales-view',
    },
];

const handlePageChange = (page: number) => {
    saleList(page);
};

onMounted(() => {
    saleList();
});
</script>
<style scoped></style>
