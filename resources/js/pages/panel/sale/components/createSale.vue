<template>
    <Sheet>
        <SheetTrigger
            class="inline-flex items-center gap-2 rounded-md border border-lime-600 bg-green-300 px-3 py-1 text-sm font-medium text-lime-800 shadow-sm transition-colors hover:bg-lime-200"
        >
            <Eye class="h-3 w-3" />
            Ver venta
        </SheetTrigger>
        <SheetContent side="bottom" class="m-10">
            <SheetHeader>
                <SheetTitle class="mx-6 flex items-start justify-between gap-6 text-lg font-semibold">
                    <span class="whitespace-nowrap">DETALLE DE LA VENTA A REALIZAR</span>
                    <div class="flex justify-between gap-4">
                        <span class="text-sm font-medium">Operación Afecta:</span>
                        <span class="font-semibold">{{ formatCurrency(operacionesGrabadas) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-sm font-medium">Operación Inafecta:</span>
                        <span class="font-semibold">{{ formatCurrency(operacionesInafectas) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-sm font-medium">IGV (18%):</span>
                        <span class="font-semibold">{{ formatCurrency(igv) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-sm font-medium">TOTAL:</span>
                        <span class="font-semibold">{{ formatCurrency(totalVenta) }}</span>
                    </div>
                </SheetTitle>

                <SheetDescription>
                    <div class="venta-detalle-container">
                        <div class="venta-detalle-section">
                            <h3 class="venta-detalle-title">Cliente</h3>
                            <div v-if="props.customerData" class="venta-detalle-info">
                                <span class="font-semibold">Nombre:</span> {{ props.customerData.firstname }} {{ props.customerData.lastname }}
                                <br />
                                <span class="font-semibold">ID:</span> {{ props.customerData.id }}
                            </div>
                            <div v-else class="venta-detalle-empty">No seleccionado</div>
                        </div>
                        <div class="venta-detalle-section">
                            <h3 class="venta-detalle-title">Doctor</h3>
                            <div v-if="props.doctorData" class="venta-detalle-info">
                                <span class="font-semibold">Nombre:</span> {{ props.doctorData.name }}
                                <br />
                                <span class="font-semibold">ID:</span> {{ props.doctorData.id }}
                            </div>
                            <div v-else class="venta-detalle-empty">No seleccionado</div>
                        </div>
                        <div class="venta-detalle-section">
                            <h3 class="venta-detalle-title">Tipo pago</h3>
                            <div v-if="props.typePaymentData" class="venta-detalle-info">
                                <span class="font-semibold">Nombre:</span> {{ props.typePaymentData.name }}
                                <br />
                                <span class="font-semibold">ID:</span> {{ props.typePaymentData.id }}
                            </div>
                            <div v-else class="venta-detalle-empty">No seleccionado</div>
                        </div>
                        <div class="venta-detalle-section">
                            <h3 class="venta-detalle-title">Tipo Voucher</h3>
                            <div v-if="props.typePaymentData" class="venta-detalle-info">
                                <span class="font-semibold">Nombre:</span> {{ props.typeVoucherData.name }}
                                <br />
                                <span class="font-semibold">ID:</span> {{ props.typeVoucherData.id }}
                            </div>
                            <div v-else class="venta-detalle-empty">No seleccionado</div>
                        </div>
                        <div class="venta-detalle-section venta-detalle-carrito">
                            <h3 class="venta-detalle-title">Carrito</h3>
                            <table v-if="props.cardProducts && props.cardProducts.length" class="venta-detalle-table">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Producto</th>
                                        <th>Composición</th>
                                        <th>Cant. Caja</th>
                                        <th>Cant. Fracción</th>
                                        <th>estado igv</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in props.cardProducts" :key="item.id">
                                        <td>{{ item.id }}</td>
                                        <td>{{ item.product_name }}</td>
                                        <td>{{ item.product_composition }}</td>
                                        <td>{{ item.quantify_box }}</td>
                                        <td>{{ item.quantify_fraction }}</td>
                                        <td>{{ item.state_igv ? 'Gravado' : 'Inafecto' }}</td>
                                        <td>S/. {{ item.unit_price * item.quantify_box + item.fraction_price * item.quantify_fraction }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-else class="venta-detalle-empty">Carrito vacío</div>
                        </div>
                    </div>
                </SheetDescription>
            </SheetHeader>
        </SheetContent>
    </Sheet>
</template>
<script setup lang="ts">
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { ComboBoxCustomer, comboBoxDoctor, TypePaymens, TypeVoucher } from '@/interface/ComboBox';
import { ProductLocalPrice } from '@/interface/ProductLocalPrice';
import { Eye } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    cardProducts: ProductLocalPrice[];
    customerData: ComboBoxCustomer;
    doctorData: comboBoxDoctor;
    typePaymentData: TypePaymens;
    typeVoucherData: TypeVoucher;
}>();
// Operaciones gravadas (con IGV)
const operacionesGrabadas = computed(() => {
    const total = props.cardProducts.reduce((total, item) => {
        if (item.state_igv) {
            // Solo productos gravados
            return (total + item.unit_price * item.quantify_box + item.fraction_price * item.quantify_fraction) / 1.18;
        }
        return total;
    }, 0);
    return parseFloat(total.toFixed(2)); // Aseguramos 2 decimales
});

// Operaciones inafectas (sin IGV)
const operacionesInafectas = computed(() => {
    const total = props.cardProducts.reduce((total, item) => {
        if (!item.state_igv) {
            // Solo productos inafectos
            return total + item.unit_price * item.quantify_box + item.fraction_price * item.quantify_fraction;
        }
        return total;
    }, 0);
    return parseFloat(total.toFixed(2)); // Aseguramos 2 decimales
});

// IGV (18% del valor base de operaciones gravadas)
const igv = computed(() => {
    const baseImponible = props.cardProducts.reduce((total, item) => {
        if (item.state_igv) {
            // Solo productos gravados
            return total + item.unit_price * item.quantify_box + item.fraction_price * item.quantify_fraction;
        }
        return total;
    }, 0);

    const igvCalculado = (baseImponible / 1.18) * 0.18;
    return parseFloat(igvCalculado.toFixed(2)); // Aseguramos 2 decimales
});

// Valor total de la venta (gravadas + inafectas)
const totalVenta = computed(() => {
    if (!props.cardProducts || props.cardProducts.length === 0) return 0.0;
    const total = operacionesGrabadas.value + operacionesInafectas.value + igv.value;
    return parseFloat(total.toFixed(2)); // Aseguramos 2 decimales
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN',
        minimumFractionDigits: 2,
    })
        .format(value)
        .replace('PEN', 'S/.');
};
</script>
<style scoped>
.venta-detalle-container {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    margin-top: 1.5rem;
}
.venta-detalle-section {
    background: #f9fafb;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.04);
    padding: 1.25rem 1.5rem;
    min-width: 220px;
    flex: 1 1 250px;
}
.venta-detalle-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2563eb;
}
.venta-detalle-info {
    font-size: 1rem;
    color: #22223b;
}
.venta-detalle-empty {
    color: #a0aec0;
    font-style: italic;
    font-size: 0.95rem;
}
.venta-detalle-carrito {
    flex-basis: 100%;
    margin-top: 1.5rem;
}
.venta-detalle-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 0.5rem;
    background: #fff;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.03);
}
.venta-detalle-table th,
.venta-detalle-table td {
    padding: 0.5rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}
.venta-detalle-table th {
    background: #f1f5f9;
    font-weight: 600;
    color: #374151;
}
.venta-detalle-table tr:last-child td {
    border-bottom: none;
}
</style>
