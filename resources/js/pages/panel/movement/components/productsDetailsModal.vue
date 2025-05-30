<template>
    <Dialog :open="modal" @update:open="closeModal">
        <DialogContent
            class="h-screen w-screen bg-gradient-to-br from-white to-emerald-50 p-8 dark:from-gray-800 dark:to-blue-900 sm:max-h-[100vh] sm:max-w-[100vw]"
        >
            <!-- Dialog Title -->
            <DialogTitle class="text-2xl font-extrabold tracking-wide text-emerald-800 dark:text-blue-200">
                Detalles del movimiento # {{ movementData.id }}
            </DialogTitle>
            <!-- Dialog Description -->
            <DialogDescription class="sr-only">
                Detalles del movimiento, incluyendo información del proveedor, fecha de emisión y lista de productos asociados.
            </DialogDescription>

            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <div class="mt-4 grid grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Código</p>
                            <p class="text-sm font-medium text-emerald-700 dark:text-blue-100">{{ movementData.code }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Proveedor</p>
                            <p class="text-sm font-medium text-emerald-700 dark:text-blue-100">{{ movementData.supplier?.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Fecha Emisión</p>
                            <p class="text-sm font-medium text-emerald-700 dark:text-blue-100">{{ formatDate(movementData.issue_date) }}</p>
                        </div>
                    </div>
                </div>
                <Button
                    variant="default"
                    class="rounded-lg bg-gradient-to-r from-emerald-500 to-emerald-600 font-semibold text-white shadow-lg transition-all duration-300 hover:from-emerald-600 hover:to-emerald-700 dark:from-blue-800 dark:to-blue-900 dark:hover:from-blue-900 dark:hover:to-blue-950"
                    @click="openAddProductModal"
                >
                    <Plus class="mr-2 h-5 w-5" />
                    Nuevo
                </Button>
            </div>

            <!-- Error Message -->
            <div v-if="errorMessage" class="mb-4 rounded-lg bg-red-100 p-4 text-red-800 dark:bg-red-900 dark:text-red-200">
                {{ errorMessage }}
            </div>

            <!-- Table Section -->
            <div class="mb-8 rounded-xl border border-emerald-200 bg-white p-6 shadow-xl dark:border-blue-700 dark:bg-gray-800">
                <div>
                    <div class="mb-6 flex items-center justify-between">
                        <span class="font-semibold text-emerald-600 dark:text-blue-400">
                            Detalle del Movimiento
                            <span class="ml-2 rounded-full bg-emerald-100 px-2 py-1 text-sm text-emerald-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ totalProducts }} productos
                            </span>
                        </span>
                        <div class="flex items-center space-x-3">
                            <SearchInput @search="onSearch" />
                        </div>
                    </div>
                    <!-- Conditional Rendering for Loading and Data Table -->
                    <SkeletonTable v-if="isLoading" :headers="10" :rowCount="5" />
                    <Table v-else>
                        <TableHeader>
                            <TableRow class="bg-emerald-100 dark:bg-blue-800">
                                <TableHead class="text-center font-semibold text-emerald-900 dark:text-blue-200">Tipo</TableHead>
                                <TableHead class="text-center font-semibold text-emerald-900 dark:text-blue-200">Cant. Cajas - Fracciones</TableHead>
                                <TableHead class="text-center font-semibold text-emerald-900 dark:text-blue-200">Producto</TableHead>
                                <TableHead class="text-center font-semibold text-emerald-900 dark:text-blue-200">Laboratorio</TableHead>
                                <TableHead class="text-center font-semibold text-emerald-900 dark:text-blue-200">Lote</TableHead>
                                <TableHead class="text-center font-semibold text-emerald-900 dark:text-blue-200">Fecha Expiración</TableHead>
                                <TableHead class="text-center font-semibold text-emerald-900 dark:text-blue-200">Precio U.</TableHead>
                                <TableHead class="text-center font-semibold text-emerald-900 dark:text-blue-200">P.U + Tax</TableHead>
                                <TableHead class="text-center font-semibold text-emerald-900 dark:text-blue-200">Total</TableHead>
                                <TableHead v-if="paginatedProducts.length > 0" class="text-center font-semibold text-emerald-900 dark:text-blue-200"
                                    >Acciones</TableHead
                                >
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="paginatedProducts.length === 0 && !isLoading">
                                <td class="py-4 text-center text-gray-600 dark:text-gray-400" colspan="10">No hay productos disponibles.</td>
                            </TableRow>
                            <TableRow
                                v-for="product in paginatedProducts"
                                :key="product.id"
                                class="transition-colors duration-200 hover:bg-emerald-50 dark:hover:bg-blue-950"
                            >
                                <td class="py-3 text-center text-gray-800 dark:text-gray-200">
                                    {{
                                        product.quantityType === 'Box'
                                            ? 'Caja'
                                            : product.quantityType === 'Both'
                                              ? 'Ambos'
                                              : product.quantityType === 'Fraction'
                                                ? 'Fracción'
                                                : product.quantityType
                                    }}
                                </td>
                                <td class="py-3 text-center text-gray-800 dark:text-gray-200">
                                    {{ product.quantity }} - {{ product.fractionQuantity }}
                                </td>
                                <td class="py-3 text-center text-gray-800 dark:text-gray-200">{{ product.productName }}</td>
                                <td class="py-3 text-center text-gray-800 dark:text-gray-200">{{ product.labName }}</td>
                                <td class="py-3 text-center text-gray-800 dark:text-gray-200">{{ product.batch }}</td>
                                <td class="py-3 text-center text-gray-800 dark:text-gray-200">{{ product.expiryDateDisplay }}</td>
                                <td class="py-3 text-center text-gray-800 dark:text-gray-200">{{ product.unitPrice }}</td>
                                <td class="py-3 text-center text-gray-800 dark:text-gray-200">{{ calculatePriceWithTax(product.unitPrice) }}</td>
                                <td class="py-3 text-center text-gray-800 dark:text-gray-200">{{ product.totalPrice }}</td>
                                <td class="flex justify-center gap-2 py-3 text-center">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="rounded-full p-2 text-blue-600 transition-colors duration-200 hover:bg-blue-100 hover:text-blue-800 dark:hover:bg-blue-900"
                                        @click="editProduct(product)"
                                    >
                                        <Pencil class="h-5 w-5" />
                                        <span class="sr-only">Editar producto</span>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="rounded-full p-2 text-red-600 transition-colors duration-200 hover:bg-red-100 hover:text-red-800 dark:hover:bg-red-900"
                                        @click="removeProduct(product.id)"
                                    >
                                        <Trash class="h-5 w-5" />
                                        <span class="sr-only">Eliminar producto</span>
                                    </Button>
                                </td>
                            </TableRow>
                        </TableBody>
                    </Table>
                    <!-- Pagination -->
                    <div v-if="!isLoading" class="mt-4">
                        <Pagination :meta="paginationMeta" @page-change="goToPage" />
                    </div>
                    <!-- Subtotal, Tax, Total -->
                    <div v-if="!isLoading" class="mt-6 flex justify-end">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Subtotal: {{ productMovements.subtotal }}</p>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Impuesto: {{ productMovements.tax }}</p>
                            <p class="text-sm font-bold text-emerald-800 dark:text-blue-200">Total: {{ productMovements.total }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex justify-end gap-4">
                <Button
                    type="button"
                    variant="outline"
                    class="rounded-lg border-emerald-300 font-semibold text-emerald-600 shadow-md transition-all duration-300 hover:bg-emerald-100 dark:border-blue-600 dark:text-blue-400 dark:hover:bg-blue-800"
                    @click="closeModal"
                >
                    Volver
                </Button>
                <Button
                    type="button"
                    variant="default"
                    class="rounded-lg bg-gradient-to-r from-emerald-500 to-emerald-600 font-semibold text-white shadow-lg transition-all duration-300 hover:from-emerald-600 hover:to-emerald-700 dark:from-blue-800 dark:to-blue-900 dark:hover:from-blue-900 dark:hover:to-blue-950"
                    @click="finalizeMovement"
                    :disabled="isLoading || paginatedProducts.length === 0"
                >
                    Finalizar
                </Button>
            </div>

            <!-- Add Product Modal -->
            <AddProductModal
                :modal="addProductModalOpen"
                :movement-id="props.movementData.id"
                @emit-close="closeAddProductModal"
                @add-product="addProductFromModal"
            />

            <!-- Edit Product Modal -->
            <EditProductModal
                :modal="editProductModalOpen"
                :movement-id="props.movementData.id"
                :product-to-edit="selectedProductToEdit"
                @emit-close="closeEditProductModal"
                @update-product="updateProductFromModal"
            />

            <ConfirmDeleteModal
                :modal="confirmDeleteModalOpen"
                :itemId="selectedProductId"
                title="Confirmar Eliminación"
                description="¿Está seguro de que desea eliminar este producto del movimiento?"
                confirmButtonText="Eliminar"
                cancelButtonText="Cancelar"
                @closeModal="closeConfirmDeleteModal"
                @deleteItem="confirmDeleteProduct"
            />
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import ConfirmDeleteModal from '@/components/delete.vue';
import SearchInput from '@/components/filter.vue';
import SkeletonTable from '@/components/loadingTable.vue';
import Pagination from '@/components/pagination.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { Pagination as PaginationMeta } from '@/interface/paginacion';
import { MovementServices } from '@/services/movementService';
import { ProductMovement, ProductMovementResponse, ProductMovementServices } from '@/services/productMovementService';
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import { MovementResource } from '../interface/Movement';
import AddProductModal from './addProductModal.vue';
import EditProductModal from './EditProductModal.vue';

const confirmDeleteModalOpen = ref(false);
const selectedProductId = ref<number | null>(null);
const isLoading = ref(false);
const addProductModalOpen = ref(false);
const editProductModalOpen = ref(false);
const selectedProductToEdit = ref<ProductMovement['data'] | null>(null);

const props = defineProps<{
    modal: boolean;
    movementData: MovementResource;
}>();

const emit = defineEmits<{
    (e: 'emit-close', open: boolean): void;
    (e: 'add-products', movementId: number, products: { product_id: number; quantity: number }[]): void;
    (e: 'refresh-movements'): void;
}>();

const productMovements = ref<ProductMovementResponse>({
    success: true,
    message: '',
    data: [],
    subtotal: '0.00',
    tax: '0.00',
    total: '0.00',
});
const errorMessage = ref<string>('');

const currentPage = ref<number>(1);
const itemsPerPage = ref<number>(5);
const searchQuery = ref<string>('');

const paginationMeta = computed<PaginationMeta>(() => ({
    per_page: itemsPerPage.value,
    total: filteredProducts.value.length,
    current_page: currentPage.value,
    last_page: Math.max(1, Math.ceil(filteredProducts.value.length / itemsPerPage.value)),
}));

const formatDate = (dateString: string) => {
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
        console.error('Error formatting date:', e);
        return '';
    }
};

const calculatePriceWithTax = (unitPrice: string) => {
    if (!unitPrice) return '0.00';
    const tax = 0.18;
    const price = parseFloat(unitPrice);
    if (props.movementData.igv_status === 1) {
        return price.toFixed(2);
    }
    return (price * (1 + tax)).toFixed(2);
};

const filteredProducts = computed(() => {
    if (!searchQuery.value) return productMovements.value.data;

    const query = searchQuery.value.toLowerCase();
    return productMovements.value.data.filter(
        (product) =>
            product.productName.toLowerCase().includes(query) ||
            product.labName.toLowerCase().includes(query) ||
            product.batch.toLowerCase().includes(query) ||
            product.quantityType.toLowerCase().includes(query),
    );
});

const totalProducts = computed(() => filteredProducts.value.length);

const paginatedProducts = computed(() => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    return filteredProducts.value.slice(startIndex, startIndex + itemsPerPage.value);
});

const goToPage = (page: number) => {
    if (page >= 1 && page <= paginationMeta.value.last_page) {
        currentPage.value = page;
    }
};

const onSearch = (text: string) => {
    searchQuery.value = text;
    currentPage.value = 1;
};

const fetchProductMovements = async () => {
    isLoading.value = true;
    try {
        const response = await ProductMovementServices.getProductMovements(props.movementData.id);
        productMovements.value = response;
        errorMessage.value = '';
    } catch (error: any) {
        console.error('Error fetching product movements:', error);
        errorMessage.value =
            error.response?.status === 404
                ? 'Movement not found.'
                : error.response?.data?.message || 'Failed to load product movements. Please try again later.';
    } finally {
        isLoading.value = false;
    }
};

const openAddProductModal = () => {
    addProductModalOpen.value = true;
};

const editProduct = (product: ProductMovement['data']) => {
    selectedProductToEdit.value = product;
    editProductModalOpen.value = true;
};

const closeAddProductModal = () => {
    addProductModalOpen.value = false;
};

const closeEditProductModal = () => {
    editProductModalOpen.value = false;
    selectedProductToEdit.value = null;
};

const addProductFromModal = async (product: ProductMovement['data']) => {
    try {
        productMovements.value.data.push(product);
        updateTotals();
        await fetchProductMovements();
        errorMessage.value = '';
        emit('refresh-movements');
        emit('add-products', props.movementData.id, [
            {
                product_id: product.productId,
                quantity: parseInt(product.totalQuantity),
            },
        ]);
    } catch (error) {
        console.error('Error adding product:', error);
        errorMessage.value = 'Failed to add product. Please try again.';
    }
    closeAddProductModal();
};

const updateProductFromModal = async (product: ProductMovement['data']) => {
    try {
        productMovements.value.data = productMovements.value.data.map((p) => (p.id === product.id ? product : p));
        updateTotals();
        await fetchProductMovements();
        errorMessage.value = '';
        emit('refresh-movements');
    } catch (error) {
        console.error('Error updating product:', error);
        errorMessage.value = 'Failed to update product. Please try again.';
    }
    closeEditProductModal();
};

const removeProduct = (id: number) => {
    console.log('Removing product movement with id:', id);
    selectedProductId.value = id;
    confirmDeleteModalOpen.value = true;
};

const confirmDeleteProduct = async () => {
    if (selectedProductId.value !== null) {
        try {
            await ProductMovementServices.deleteProductMovement(selectedProductId.value);
            productMovements.value.data = productMovements.value.data.filter((p) => p.id !== selectedProductId.value);
            updateTotals();
            if (paginatedProducts.value.length === 0 && currentPage.value > 1) {
                currentPage.value--;
            }
            emit('refresh-movements');
        } catch (error: any) {
            console.error('Error deleting product movement:', error);
            errorMessage.value = error.response?.data?.message || 'Failed to delete product. Please try again.';
        }
        closeConfirmDeleteModal();
    }
};

const closeConfirmDeleteModal = () => {
    confirmDeleteModalOpen.value = false;
    selectedProductId.value = null;
};

const updateTotals = () => {
    const tasaIgv = 0.18;
    let subtotal = 0;
    let tax = 0;
    let total = 0;

    productMovements.value.data.forEach((p) => {
        const totalPrice = parseFloat(p.totalPrice) || 0;
        if (props.movementData.igv_status === 1) {
            const sub = totalPrice / (1 + tasaIgv);
            const igv = totalPrice - sub;
            subtotal += sub;
            tax += igv;
            total += totalPrice;
        } else {
            const sub = totalPrice;
            const igv = sub * tasaIgv;
            subtotal += sub;
            tax += igv;
            total += sub + igv;
        }
    });

    productMovements.value.subtotal = subtotal.toFixed(2);
    productMovements.value.tax = tax.toFixed(2);
    productMovements.value.total = total.toFixed(2);
};

const finalizeMovement = async () => {
    if (paginatedProducts.value.length === 0) {
        errorMessage.value = 'No products to finalize.';
        return;
    }
    isLoading.value = true;
    try {
        await MovementServices.finalizeMovement(props.movementData.id);
        errorMessage.value = '';
        emit('refresh-movements');
        closeModal();
    } catch (error: any) {
        console.error('Error finalizing movement:', error);
        errorMessage.value = error.response?.data?.message || 'Failed to finalize movement. Please try again.';
    } finally {
        isLoading.value = false;
    }
};

const onSubmit = () => {
    const products = productMovements.value.data.map((p) => ({
        product_id: p.productId,
        quantity: parseInt(p.totalQuantity),
    }));
    emit('add-products', props.movementData.id, products);
    closeModal();
};

const closeModal = () => {
    productMovements.value = { success: true, message: '', data: [], subtotal: '0.00', tax: '0.00', total: '0.00' };
    errorMessage.value = '';
    currentPage.value = 1;
    searchQuery.value = '';
    selectedProductToEdit.value = null;
    editProductModalOpen.value = false;
    addProductModalOpen.value = false;
    emit('emit-close', false);
};

onMounted(() => {
    if (props.modal && props.movementData.id) {
        fetchProductMovements();
    }
});
</script>
