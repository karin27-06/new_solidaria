<template>
    <div class="container-table">
        <h2 class="mb-1 text-center text-2xl font-bold text-primary">Listado de Ventas</h2>
        <div v-if="loading">
            <p class="py-8 text-center text-gray-500">Cargando ventas...</p>
        </div>
        <div v-else class="table-content">
            <div class="table-container table-wrapper">
                <table class="table-responsive">
                    <thead>
                        <tr class="table-header-row">
                            <th class="table-head-id">ID</th>
                            <th class="table-head">Cliente</th>
                            <th class="table-head">Usuario</th>
                            <th class="table-head">Local</th>
                            <th class="table-head">Doctor</th>
                            <th class="table-head">Tipo Comprobante</th>
                            <th class="table-head">Tipo Pago</th>
                            <th class="table-head">Código</th>
                            <th class="table-head">Op. Gravada</th>
                            <th class="table-head">Op. Inafecta</th>
                            <th class="table-head">IGV</th>
                            <th class="table-head">Total</th>
                            <th class="table-head-status">Estado Venta</th>
                            <th class="table-head-status">Estado Sunat</th>
                            <th class="table-head">Fecha</th>
                            <th class="table-head-actions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <tr v-for="sale in salesList" :key="sale.id" class="table-row">
                            <td class="cell-id">{{ sale.id }}</td>
                            <td class="cell-data">{{ sale.cliente }}</td>
                            <td class="cell-data">{{ sale.usuario }}</td>
                            <td class="cell-data">{{ sale.local }}</td>
                            <td class="cell-data">{{ sale.doctor }}</td>
                            <td class="cell-data">{{ sale.tipo_comprobante }}</td>
                            <td class="cell-data">{{ sale.tipo_pago }}</td>
                            <td class="cell-data">{{ sale.codigo }}</td>
                            <td class="cell-data highlight-amount-green">{{ sale.op_gravada }}</td>
                            <td class="cell-data highlight-amount-blue">{{ sale.op_inafecta }}</td>
                            <td class="cell-data highlight-amount-yellow">{{ sale.igv }}</td>
                            <td class="cell-total highlight-amount-green">{{ sale.total }}</td>
                            <td class="cell-status">
                                <span v-if="sale.estado_venta === true" class="status-badge status-active">
                                    <span class="status-indicator status-indicator-active"></span>
                                    {{ sale.estado_venta }}
                                </span>
                                <span v-else class="status-badge status-inactive">
                                    <span class="status-indicator status-indicator-inactive"></span>
                                    {{ sale.estado_venta }}
                                </span>
                            </td>
                            <td class="cell-status">
                                <span v-if="sale.estado_sunat === true" class="status-badge status-active">
                                    <span class="status-indicator status-indicator-active"></span>
                                    {{ sale.estado_sunat }}
                                </span>
                                <span v-else class="status-badge status-inactive">
                                    <span class="status-indicator status-indicator-inactive"></span>
                                    {{ sale.estado_sunat }}
                                </span>
                            </td>
                            <td class="cell-data">{{ sale.created_at }}</td>
                            <td class="cell-actions">
                                <div class="actions-container">
                                    <Button variant="ghost" size="sm" class="action-button" @click="$emit('xml', sale.id)" title="Descargar XML">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="action-icon"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 16h8M8 12h8m-8-4h8M4 6h16M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6"
                                            />
                                        </svg>
                                    </Button>
                                    <Button variant="ghost" size="sm" class="action-button" @click="$emit('cdr', sale.id)" title="Descargar CDR">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="action-icon"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </Button>
                                    <Button variant="ghost" size="sm" class="action-button-2" @click="$emit('detalle', sale.id)" title="Ver Detalle">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="action-icon"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination-container" v-if="paginationSale">
                <div class="pagination-summary">
                    Mostrando <span class="pagination-emphasis">{{ paginationSale.from || 0 }}</span> a
                    <span class="pagination-emphasis">{{ paginationSale.to || 0 }}</span> de
                    <span class="pagination-emphasis">{{ paginationSale.total }}</span> ventas
                </div>
                <PaginationUser
                    :meta="{
                        ...paginationSale,
                        per_page: paginationSale.per_page || 10,
                        current_page: paginationSale.current_page || 1,
                        last_page: paginationSale.last_page || 1,
                    }"
                    @page-change="$emit('page-change', $event)"
                />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import PaginationUser from '@/components/pagination.vue';
import { Button } from '@/components/ui/button';
import { SaleResource } from '../../sale/interface/Sale';

const emit = defineEmits<{
    (e: 'page-change', page: number): void;
    (e: 'xml', id: number): void;
    (e: 'cdr', id: number): void;
    (e: 'detalle', id: number): void;
}>();

const { salesList, paginationSale, loading } = defineProps<{
    salesList: SaleResource[];
    paginationSale: {
        from: number;
        to: number;
        total: number;
        per_page?: number;
        current_page?: number;
        last_page?: number;
    };
    loading: boolean;
}>();
</script>
<style scoped>
.table-content {
    background: #fff;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.04);
    padding: 1.25rem 0.5rem;
}

.table-responsive th,
.table-responsive td {
    vertical-align: middle;
}

.actions-container .action-button,
.actions-container .action-button-2 {
    min-width: 2.5rem;
    margin: 0 2px;
    font-size: 0.85rem;
    font-weight: 500;
    border-radius: 0.375rem;
    transition:
        background 0.2s,
        color 0.2s;
}

.actions-container .action-button-2 {
    color: #dc2626;
    border-color: #dc2626;
}

.actions-container .action-button-2:hover {
    background: #fee2e2;
    color: #b91c1c;
}

.actions-container .action-button:hover {
    background: #d1fae5;
    color: #047857;
}

.status-badge {
    min-width: 80px;
    justify-content: center;
}

.highlight-amount-green {
    color: #059669;
    font-weight: 600;
    background: #ecfdf5;
    border-radius: 0.375rem;
    padding: 0.2rem 0.5rem;
}
.highlight-amount-blue {
    color: #2563eb;
    font-weight: 600;
    background: #eff6ff;
    border-radius: 0.375rem;
    padding: 0.2rem 0.5rem;
}
.highlight-amount-yellow {
    color: #ca8a04;
    font-weight: 600;
    background: #fef9c3;
    border-radius: 0.375rem;
    padding: 0.2rem 0.5rem;
}
.highlight-amount-total {
    color: #fff;
    background: #16a34a;
    font-weight: bold;
    border-radius: 0.375rem;
    padding: 0.2rem 0.5rem;
}

@media (max-width: 900px) {
    .table-content {
        padding: 0.5rem 0.1rem;
    }
    .table-responsive th,
    .table-responsive td {
        padding: 0.3rem 0.3rem;
        font-size: 0.85rem;
    }
}
</style>
