<template>
    <div class="container-table">
        <LoadingTable v-if="loading" :headers="8" :row-count="12" />
        <div v-else class="table-content">
            <div class="table-container">
                <div class="table-responsive">
                    <Table>
                        <TableHeader class="table-header-row">
                            <TableRow>
                                <TableHead class="table-head-id">ID</TableHead>
                                <TableHead class="table-head">Nombre</TableHead>
                                <TableHead class="table-head">Composición</TableHead>
                                <TableHead class="table-head">Presentación</TableHead>
                                <TableHead class="table-head">Laboratorio</TableHead>
                                <TableHead class="table-head">Categoría</TableHead>
                                <TableHead class="table-head">Cajas</TableHead>
                                <TableHead class="table-head">Fracciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody class="table-body">
                            <TableRow v-for="product in productList" :key="product.id" class="table-row">
                                <td class="cell-id text-center">{{ product.id }}</td>
                                <td class="cell-data text-center">{{ product.nombre }}</td>
                                <td class="cell-data text-center">{{ product.composicion }}</td>
                                <td class="cell-data text-center">{{ product.presentacion }}</td>
                                <td class="cell-data text-center">{{ product.laboratorio }}</td>
                                <td class="cell-data text-center">{{ product.categoria }}</td>
                                <td class="cell-data text-center">
                                    <span
                                        class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                    >
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-blue-500 dark:bg-blue-400"></span>
                                        {{ product.cajas }}
                                    </span>
                                </td>
                                <td class="cell-data text-center">
                                    <span
                                        class="inline-flex items-center rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-300"
                                    >
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-purple-500 dark:bg-purple-400"></span>
                                        {{ product.fracciones }}
                                    </span>
                                </td>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
            <div class="pagination-container">
                <div class="pagination-summary">
                    Mostrando <span class="pagination-emphasis">{{ productPaginate.from || 0 }}</span> a
                    <span class="pagination-emphasis">{{ productPaginate.to || 0 }}</span> de
                    <span class="pagination-emphasis">{{ productPaginate.total }}</span> productos
                </div>
                <PaginationInventory :meta="productPaginate" @page-change="$emit('page-change', $event)" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import LoadingTable from '@/components/loadingTable.vue';
import PaginationInventory from '@/components/pagination.vue';
import { Table, TableBody, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useToast } from '@/components/ui/toast';
import { Pagination } from '@/interface/paginacion';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { InventoryResource } from '../interface/Inventory';

const { toast } = useToast();

// const emit = defineEmits<{
//     (e: 'page-change', page: number): void;
// }>();

const page = usePage<SharedData>();

const message = ref(page.props.flash?.message || '');

onMounted(() => {
    if (message.value) {
        toast({
            title: 'Notificación',
            description: message.value,
        });
    }
});

const { productList, productPaginate, loading } = defineProps<{
    productList: InventoryResource[];
    productPaginate: Pagination;
    loading: boolean;
}>();
</script>

<style scoped>
.container-table {
    padding: 1rem;
}

.table-content {
    width: 100%;
}

.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding: 0.5rem;
}

.pagination-summary {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.pagination-emphasis {
    font-weight: 600;
    color: var(--text-primary);
}
</style>
