<template>
    <div class="container-table">
        <LoadingTable v-if="loading" :headers="13" :row-count="12" />
        <div v-else class="table-content">
            <div class="table-container">
                <div class="table-responsive">
                    <!-- Tabla para PC -->
                    <Table class="hidden md:table w-full table-fixed">
                        <TableHeader class="table-header-row bg-gray-100 dark:bg-gray-800">
                            <TableRow>
                                <TableHead class="table-head-id text-center w-12">ID</TableHead>
                                <TableHead class="table-head text-center w-20">Código</TableHead>
                                <TableHead class="table-head text-center w-24">Fecha Emisión</TableHead>
                                <TableHead class="table-head text-center w-24">Fecha Crédito</TableHead>
                                <TableHead class="table-head text-center w-28">Proveedor</TableHead>
                                <TableHead class="table-head text-center w-24">Usuario</TableHead>
                                <TableHead class="table-head text-center w-24">Tipo Movimiento</TableHead>
                                <TableHead class="table-head-status text-center w-20">Estado</TableHead>
                                <TableHead class="table-head-status text-center w-20">Estado IGV</TableHead>
                                <TableHead class="table-head text-center w-20">Tipo Pago</TableHead>
                                <TableHead class="table-head text-center w-20">Subtotal</TableHead>
                                <TableHead class="table-head text-center w-20">IGV</TableHead>
                                <TableHead class="table-head text-center w-20">Total</TableHead>
                                <TableHead class="table-head-actions text-center w-24">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody class="table-body">
                            <TableRow v-for="movement in movementList" :key="movement.id" class="table-row">
                                <td class="cell-id text-center truncate">{{ movement.id }}</td>
                                <td class="cell-data text-center truncate">{{ movement.code }}</td>
                                <td class="cell-data text-center truncate">{{ formatDate(movement.issue_date) }}</td>
                                <td class="cell-data text-center truncate">{{ formatDate(movement.credit_date) }}</td>
                                <td class="cell-data text-center truncate">{{ movement.supplier.name }}</td>
                                <td class="cell-data text-center truncate">{{ movement.user.name }}</td>
                                <td class="cell-data text-center truncate">
                                    <span :class="getTypeMovementClass(movement.typemovement.name)"
                                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full"></span>
                                        {{ movement.typemovement.name }}
                                    </span>
                                </td>
                                <td class="cell-status text-center truncate">
                                    <span v-if="movement.status === 1"
                                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-green-500 dark:bg-green-400"></span>
                                        Activo
                                    </span>
                                    <span v-if="movement.status === 0"
                                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-red-500 dark:bg-red-400"></span>
                                        Eliminado
                                    </span>
                                    <span v-if="movement.status === 2"
                                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-orange-500 dark:bg-orange-400"></span>
                                        Anulado
                                    </span>
                                    <span v-if="movement.status === 3"
                                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-purple-500 dark:bg-purple-400"></span>
                                        Finalizado
                                    </span>
                                </td>
                                <td class="cell-status text-center truncate">
                                    <span v-if="movement.igv_status === 1"
                                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-blue-500 dark:bg-blue-400"></span>
                                        Con IGV
                                    </span>
                                    <span v-else
                                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-gray-500 dark:bg-gray-400"></span>
                                        Sin IGV
                                    </span>
                                </td>
                                <td class="cell-data text-center truncate">
                                    <span :class="getPaymentTypeClass(movement.payment_type)"
                                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full"></span>
                                        {{ movement.payment_type === 'contado' ? 'Contado' : 'Crédito' }}
                                    </span>
                                </td>
                                <td class="cell-data text-center truncate">{{ movement.subtotal }}</td>
                                <td class="cell-data text-center truncate">{{ movement.igv }}</td>
                                <td class="cell-data text-center truncate">{{ movement.total }}</td>
                                <td class="cell-actions text-center">
                                    <div class="actions-container flex justify-center gap-1">
                                        <Button
                                            v-if="movement.status === 1"
                                            variant="ghost"
                                            size="sm"
                                            class="action-button p-1"
                                            @click="openModal(movement.id)"
                                            title="Editar movimiento"
                                        >
                                            <UserPen class="action-icon h-4 w-4" />
                                            <span class="sr-only">Editar movimiento</span>
                                        </Button>
                                        <Button
                                            v-if="movement.status !== 2 && movement.status !== 3"
                                            variant="ghost"
                                            size="sm"
                                            class="action-button p-1"
                                            @click="openModalProductsDetails(movement.id)"
                                            title="Agregar productos"
                                        >
                                            <PackagePlus class="action-icon h-4 w-4" />
                                            <span class="sr-only">Agregar productos</span>
                                        </Button>
                                        <Button
                                            v-if="movement.status !== 3"
                                            variant="ghost"
                                            size="sm"
                                            class="action-button p-1"
                                            @click="openModalDelete(movement.id)"
                                            title="Eliminar movimiento"
                                        >
                                            <Trash class="action-icon h-4 w-4" />
                                            <span class="sr-only">Eliminar movimiento</span>
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="action-button p-1"
                                            @click="openPrintModal(movement)"
                                            title="Imprimir comprobante"
                                        >
                                            <Printer class="action-icon h-4 w-4" />
                                            <span class="sr-only">Imprimir comprobante</span>
                                        </Button>
                                    </div>
                                </td>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Diseño apilado para móviles -->
                    <div class="md:hidden space-y-4 px-2">
                        <div v-for="movement in movementList" :key="movement.id" class="border rounded-lg p-3 bg-white dark:bg-gray-800 shadow-sm">
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="font-medium text-gray-700 dark:text-gray-200">ID</div>
                                <div class="cell-id truncate">{{ movement.id }}</div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">Código</div>
                                <div class="cell-data truncate">{{ movement.code }}</div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">Fecha Emisión</div>
                                <div class="cell-data truncate">{{ formatDate(movement.issue_date) }}</div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">Fecha Crédito</div>
                                <div class="cell-data truncate">{{ formatDate(movement.credit_date) }}</div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">Proveedor</div>
                                <div class="cell-data truncate">{{ movement.supplier.name }}</div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">Usuario</div>
                                <div class="cell-data truncate">{{ movement.user.name }}</div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">Tipo Movimiento</div>
                                <div class="cell-data">
                                    <span :class="getTypeMovementClass(movement.typemovement.name)" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full"></span>
                                        {{ movement.typemovement.name }}
                                    </span>
                                </div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">Estado</div>
                                <div class="cell-status">
                                    <span v-if="movement.status === 1" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-green-500 dark:bg-green-400"></span>
                                        Activo
                                    </span>
                                    <span v-if="movement.status === 0" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-red-500 dark:bg-red-400"></span>
                                        Eliminado
                                    </span>
                                    <span v-if="movement.status === 2" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-orange-500 dark:bg-orange-400"></span>
                                        Anulado
                                    </span>
                                    <span v-if="movement.status === 3" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-purple-500 dark:bg-green-400"></span>
                                        Finalizado
                                    </span>
                                </div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">Estado IGV</div>
                                <div class="cell-status">
                                    <span v-if="movement.igv_status === 1" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-blue-500 dark:bg-blue-400"></span>
                                        Con IGV
                                    </span>
                                    <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-gray-500 dark:bg-gray-400"></span>
                                        Sin IGV
                                    </span>
                                </div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">Tipo Pago</div>
                                <div class="cell-data">
                                    <span :class="getPaymentTypeClass(movement.payment_type)" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full"></span>
                                        {{ movement.payment_type === 'contado' ? 'Contado' : 'Crédito' }}
                                    </span>
                                </div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">Subtotal</div>
                                <div class="cell-data truncate">{{ movement.subtotal }}</div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">IGV</div>
                                <div class="cell-data truncate">{{ movement.igv }}</div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">Total</div>
                                <div class="cell-data truncate">{{ movement.total }}</div>
                            </div>
                            <div class="mt-3 actions-container flex justify-center gap-1">
                                <Button
                                    v-if="movement.status === 1"
                                    variant="ghost"
                                    size="sm"
                                    class="action-button p-1"
                                    @click="openModal(movement.id)"
                                    title="Editar movimiento"
                                >
                                    <UserPen class="action-icon h-4 w-4" />
                                    <span class="sr-only">Editar movimiento</span>
                                </Button>
                                <Button
                                    v-if="movement.status !== 2 && movement.status !== 3"
                                    variant="ghost"
                                    size="sm"
                                    class="action-button p-1"
                                    @click="openModalProductsDetails(movement.id)"
                                    title="Agregar productos"
                                >
                                    <PackagePlus class="action-icon h-4 w-4" />
                                    <span class="sr-only">Agregar productos</span>
                                </Button>
                                <Button
                                    v-if="movement.status !== 3"
                                    variant="ghost"
                                    size="sm"
                                    class="action-button p-1"
                                    @click="openModalDelete(movement.id)"
                                    title="Eliminar movimiento"
                                >
                                    <Trash class="action-icon h-4 w-4" />
                                    <span class="sr-only">Eliminar movimiento</span>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="action-button p-1"
                                    @click="openPrintModal(movement)"
                                    title="Imprimir comprobante"
                                >
                                    <Printer class="action-icon h-4 w-4" />
                                    <span class="sr-only">Imprimir comprobante</span>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pagination-container mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2 px-2">
                <div class="pagination-summary text-xs text-gray-600 dark:text-gray-300">
                    Mostrando <span class="pagination-emphasis">{{ movementPaginate.from || 0 }}</span> a
                    <span class="pagination-emphasis">{{ movementPaginate.to || 0 }}</span> de
                    <span class="pagination-emphasis">{{ movementPaginate.total }}</span> movimientos
                </div>
                <PaginationMovement :meta="movementPaginate" @page-change="$emit('page-change', $event)" />
            </div>
        </div>
        <!-- Print Modal -->
        <PrintReceiptModal
            v-if="showPrintModal"
            :movement="selectedMovement"
            @close="closePrintModal"
        />
    </div>
</template>

<script setup lang="ts">
import LoadingTable from '@/components/loadingTable.vue';
import PaginationMovement from '@/components/pagination.vue';
import PrintReceiptModal from '@/components/PrintReceiptModal.vue';
import Button from '@/components/ui/button/Button.vue';
import { Table, TableBody, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useToast } from '@/components/ui/toast';
import { Pagination } from '@/interface/paginacion';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { PackagePlus, Trash, UserPen, Printer } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { MovementResource } from '../interface/Movement';

const { toast } = useToast();

const emit = defineEmits<{
    (e: 'page-change', page: number): void;
    (e: 'open-modal', id_movement: number): void;
    (e: 'open-modal-delete', id_movement: number): void;
    (e: 'open-modal-products-details', id_movement: number): void;
}>();

const page = usePage<SharedData>();

const message = ref(page.props.flash?.message || '');

const showPrintModal = ref(false);
const selectedMovement = ref<MovementResource | null>(null);

onMounted(() => {
    if (message.value) {
        toast({
            title: 'Notificación',
            description: message.value,
        });
    }
});

const { movementList, movementPaginate } = defineProps<{
    movementList: MovementResource[];
    movementPaginate: Pagination;
    loading: boolean;
}>();

const openModal = (id: number) => {
    emit('open-modal', id);
};

const openModalDelete = (id: number) => {
    emit('open-modal-delete', id);
};

const openModalProductsDetails = (id: number) => {
    emit('open-modal-products-details', id);
};

const openPrintModal = (movement: MovementResource) => {
    selectedMovement.value = movement;
    showPrintModal.value = true;
};

const closePrintModal = () => {
    showPrintModal.value = false;
    selectedMovement.value = null;
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    
    try {
        if (dateString.includes('T')) {
            return dateString.split('T')[0];
        }
        
        if (dateString.includes('/')) {
            const [day, month, year] = dateString.split('/');
            return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
        }
        
        return dateString;
    } catch (e) {
        console.error('Error al formatear la fecha:', e);
        return '';
    }
};

const getPaymentTypeClass = (tipoPago) => {
    if (tipoPago === 'contado') {
        return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 [&>span]:bg-green-500 dark:[&>span]:bg-green-400';
    } else {
        return 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 [&>span]:bg-purple-500 dark:[&>span]:bg-purple-400';
    }
};

const getTypeMovementClass = (tipoMovimiento) => {
    switch(tipoMovimiento) {
        case 'Factura':
            return 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 [&>span]:bg-cyan-500 dark:[&>span]:bg-cyan-400';
        case 'Guía':
            return 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 [&>span]:bg-amber-500 dark:[&>span]:bg-amber-400';
        case 'Boleta':
            return 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 [&>span]:bg-pink-500 dark:[&>span]:bg-pink-400';
        case 'Venta':
            return 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 [&>span]:bg-sky-500 dark:[&>span]:bg-sky-400';
        default:
            return 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 [&>span]:bg-slate-500 dark:[&>span]:bg-slate-400';
    }
};
</script>

<style scoped>
.container-table {
    @apply w-full max-w-full overflow-x-auto;
}

.table-container {
    @apply w-full;
}

.table-responsive {
    @apply w-full;
}

.table-fixed {
    table-layout: fixed;
}

.table-head, .cell-data, .cell-id, .cell-status, .cell-actions {
    @apply px-2 py-1 text-xs;
}

.cell-data, .cell-status {
    @apply truncate;
}

.actions-container {
    @apply flex items-center justify-center gap-1;
}

.action-button {
    @apply p-1;
}

.action-icon {
    @apply h-4 w-4;
}

.table-header-row {
    @apply sticky top-0 z-10;
}

.pagination-container {
    @apply mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2;
}

.pagination-summary {
    @apply text-xs text-gray-600 dark:text-gray-300;
}

.pagination-emphasis {
    @apply font-medium;
}
</style>