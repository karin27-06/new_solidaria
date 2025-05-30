<template>
    <div class="table-container">
        <Table class="table-responsive">
            <TableHeader class="table-header-row">
                <TableRow>
                    <TableHead class="table-head-id">ID</TableHead>
                    <TableHead class="table-head text-center font-bold">Nombre[Composición]</TableHead>
                    <TableHead class="table-head">Fraccion</TableHead>
                    <TableHead class="table-head-status">status fraccion</TableHead>
                    <TableHead class="table-head">Cajas</TableHead>
                    <TableHead class="table-head">Fracciones</TableHead>
                    <TableHead class="table-head">P.caja</TableHead>
                    <TableHead class="table-head">P.fraccion</TableHead>
                    <TableHead class="table-head">C.cajas</TableHead>
                    <TableHead class="table-head">C.fracciones</TableHead>
                    <TableHead class="table-head-actions">Agregar</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody class="table-body">
                <TableRow v-for="product in editableProducts" :key="product.id" class="table-row">
                    <td class="cell-id">{{ product.id }}</td>
                    <td class="cell-data">{{ product.product_name }}[{{ product.product_composition }}]</td>
                    <td class="cell-data">X{{ product.fraction }}</td>
                    <td class="cell-status">
                        <span v-if="product.states_fraction === true" class="status-badge status-active">
                            <span class="status-indicator status-indicator-active"></span>
                            Activo
                        </span>
                        <span v-else class="status-badge status-inactive">
                            <span class="status-indicator status-indicator-inactive"></span>
                            Inactivo
                        </span>
                    </td>
                    <td class="cell-data">{{ product.stockBox }}</td>
                    <td class="cell-data">{{ product.stockFraction }}</td>
                    <td class="cell-data">S/{{ product.unit_price }}</td>
                    <td class="cell-data">S/{{ product.fraction_price }}</td>
                    <td class="cell-data">
                        <Input
                            type="number"
                            min="0"
                            :max="product.stockBox"
                            v-model.number="product.quantify_box"
                            @input="onBoxInput(product)"
                            class="input-qty"
                            :title="`Máximo: ${product.stockBox}`"
                        />
                    </td>
                    <td class="cell-data">
                        <Input
                            type="number"
                            v-model.number="product.quantify_fraction"
                            @input="onFractionInput(product)"
                            class="input-qty"
                            :disabled="!product.states_fraction"
                            :title="product.states_fraction ? `Máximo: ${product.stockFraction}` : 'Fracciones deshabilitadas'"
                        />
                    </td>
                    <td class="cell-actions">
                        <div class="actions-container">
                            <Button
                                variant="secondary"
                                size="sm"
                                class="action-button bg-green-200"
                                @click="addProduct(product)"
                                title="Agregar producto"
                            >
                                <CirclePlus class="action-icon" />
                                <span class="sr-only">agregar producto</span>
                            </Button>
                        </div>
                    </td>
                </TableRow>
            </TableBody>
        </Table>
    </div>
    <div class="pagination-container">
        <div class="pagination-summary">
            Mostrando <span class="pagination-emphasis">{{ props.resultProductsPaginate.from || 0 }}</span> a
            <span class="pagination-emphasis">{{ props.resultProductsPaginate.to || 0 }}</span> de
            <span class="pagination-emphasis">{{ props.resultProductsPaginate.total }}</span> productos
        </div>
        <PaginationResuls :meta="props.resultProductsPaginate" @page-change="$emit('page-change', $event)" />
    </div>
</template>
<script setup lang="ts">
import PaginationResuls from '@/components/pagination.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { Table, TableBody, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pagination } from '@/interface/paginacion';
import { ProductLocalPrice } from '@/interface/ProductLocalPrice';
import { CirclePlus } from 'lucide-vue-next';
import { ref, toRaw, watch } from 'vue';
const props = defineProps<{
    resultProducts: ProductLocalPrice[];
    resultProductsPaginate: Pagination;
}>();

const emits = defineEmits<{
    (e: 'add-product', product: ProductLocalPrice): void;
    (e: 'page-change', page: number): void;
}>();

// Make a local copy to allow editing
const editableProducts = ref(props.resultProducts.map((p) => ({ ...p })));

watch(
    () => props.resultProducts,
    (newVal) => {
        editableProducts.value = newVal.map((p) => ({ ...p }));
    },
);

const addProduct = (product: ProductLocalPrice) => {
    console.log('Adding product:', toRaw(product));
    emits('add-product', product);
};

function onBoxInput(product: ProductLocalPrice) {
    // Clamp to stock
    if (product.quantify_box > product.stockBox) product.quantify_box = 0;
    if (product.quantify_box < 0) product.quantify_box = 0;
}

function onFractionInput(product: ProductLocalPrice) {
    if (!product.states_fraction) return;
    if (product.quantify_fraction < 0) product.quantify_fraction = 0;
    if (product.quantify_fraction > product.stockFraction) {
        const fractionPerBox = product.fraction || 1;
        let totalFractions = product.quantify_fraction;
        let extraBoxes = Math.floor(totalFractions / fractionPerBox);
        let remainingFractions = totalFractions % fractionPerBox;
        let maxAddableBoxes = product.stockBox - product.quantify_box;
        if (extraBoxes > maxAddableBoxes) {
            extraBoxes = maxAddableBoxes;
            remainingFractions = product.stockFraction;
        }
        product.quantify_box += extraBoxes;
        product.quantify_fraction = remainingFractions;
    }
}
</script>
<style scoped>
.input-qty {
    width: 60px;
    padding: 2px 6px;
    border: 1px solid #abec6e;
    border-radius: 4px;
    text-align: center;
}
</style>
