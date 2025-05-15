<script setup lang="ts">
import CustomerChart from '@/components/Charts/CustomerChart.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const columnsX = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
const columnsY = [30, 40, 45, 50, 49, 60, 70];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-auto overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <CustomerChart :columns-x="columnsX" :columns-y="columnsY" title="USUARIOS" subtitle="Usuarios por local" chart-type="area" />
                </div>
                <div class="relative aspect-auto overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <CustomerChart
                        :columns-x="columnsX"
                        :columns-y="columnsY"
                        title="PRODUCTOS"
                        subtitle="Productos por local"
                        chart-type="scatter"
                    />
                </div>
                <div class="relative aspect-auto overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <CustomerChart :columns-x="columnsX" :columns-y="columnsY" title="GUIAS" subtitle="guias por local" chart-type="bar" />
                </div>
            </div>
            <div class="relative h-[400px] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <CustomerChart
                    :columns-x="columnsX"
                    :columns-y="columnsY"
                    title="VENTAS"
                    subtitle="Ventas por vendedor del local"
                    chart-type="radar"
                />
            </div>
        </div>
    </AppLayout>
</template>
