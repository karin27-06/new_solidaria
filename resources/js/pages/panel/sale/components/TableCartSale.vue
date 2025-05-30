<template>
    <div v-if="!props.resultProducts.length">
        <EmptyCart />
    </div>
    <div v-else class="table-container">
        <Table class="table-responsive">
            <TableHeader class="table-header-row">
                <TableRow>
                    <TableHead class="table-head-id">ID</TableHead>
                    <TableHead class="table-head text-center font-bold">Nombre[Composición]</TableHead>
                    <TableHead class="table-head">C.cajas</TableHead>
                    <TableHead class="table-head">C.fracciones</TableHead>
                    <TableHead class="table-head">P.caja</TableHead>
                    <TableHead class="table-head">P.fraccion</TableHead>
                    <TableHead class="table-head">sub.total</TableHead>
                    <TableHead class="table-head-actions">Eliminar</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody class="table-body">
                <TableRow v-for="product in resultProducts" :key="product.id" class="table-row">
                    <td class="cell-id">{{ product.id }}</td>
                    <td class="cell-data">{{ product.product_name }}[{{ product.product_composition }}]</td>
                    <td class="cell-data">
                        <Input
                            type="number"
                            min="0"
                            :max="product.stockBox"
                            v-model.number="product.quantify_box"
                            @input="$emit('add-product', product)"
                            class="input-qty"
                            :title="`Máximo: ${product.stockBox}`"
                        />
                    </td>
                    <td class="cell-data">
                        <Input
                            type="number"
                            v-model.number="product.quantify_fraction"
                            @input="$emit('add-product', product)"
                            class="input-qty"
                            :disabled="!product.states_fraction"
                            :title="product.states_fraction ? `Máximo: ${product.stockFraction}` : 'Fracciones deshabilitadas'"
                        />
                    </td>
                    <td class="cell-data">S/{{ product.unit_price }}</td>
                    <td class="cell-data">S/{{ product.fraction_price }}</td>
                    <td class="cell-data">S/{{ product.unit_price * product.quantify_box + product.fraction_price * product.quantify_fraction }}</td>
                    <td class="cell-actions">
                        <Button variant="destructive" size="icon" @click="removeProducto(product)">
                            <TrashIcon class="icon-trash" />
                        </Button>
                    </td>
                </TableRow>
            </TableBody>
            <TableFooter class="table-footer">
                <TableRow>
                    <td colspan="8" class="cell-total">
                        <div class="grid w-full grid-cols-8">
                            <span class="col-span-6 text-right font-semibold text-gray-700">Total:</span>
                            <span class="col-span-2 text-center font-bold text-black">
                                S/{{
                                    resultProducts
                                        .reduce(
                                            (acc, product) =>
                                                acc +
                                                (product.unit_price * product.quantify_box + product.fraction_price * product.quantify_fraction),
                                            0,
                                        )
                                        .toFixed(2)
                                }}
                            </span>
                        </div>
                    </td>
                </TableRow>
            </TableFooter>
        </Table>
    </div>
</template>
<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { Table, TableBody, TableFooter, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ProductLocalPrice } from '@/interface/ProductLocalPrice';
import { TrashIcon } from 'lucide-vue-next';
import EmptyCart from './EmptyCart.vue';
const props = defineProps<{
    resultProducts: ProductLocalPrice[];
}>();

const emit = defineEmits<{
    (e: 'remove-product', product: ProductLocalPrice): void;
    (e: 'add-product', product: ProductLocalPrice): void;
}>();

const removeProducto = (product: ProductLocalPrice) => {
    emit('remove-product', product);
};
</script>
<style scoped></style>
