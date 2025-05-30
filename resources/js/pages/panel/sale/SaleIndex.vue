<template>
    <Head title="Proceso de Ventas" />
    <AppLayout :breadcrumbs="breadcrumbs" :visible="false">
        <div class="relative flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Paneles de contenido -->
            <div class="relative z-10 min-h-[100vh] flex-1 rounded-xl border border-muted dark:border-muted-foreground md:min-h-min">
                <ResizablePanelGroup id="ventas-layout" direction="vertical">
                    <!-- Buscador -->
                    <ResizablePanel id="panel-buscador" :default-size="9">
                        <div class="h-full overflow-auto rounded-lg p-4 shadow">
                            <div class="text-center text-sm font-semibold">
                                <SearchSale @search="searchFather" @delete-result="deleteResult" />
                            </div>
                        </div>
                    </ResizablePanel>

                    <ResizableHandle with-handle />

                    <!-- Resultados -->
                    <ResizablePanel id="panel-resultados" :default-size="48">
                        <div class="h-full overflow-auto rounded-lg p-4 shadow">
                            <div class="text-center text-sm font-semibold">
                                <TableResultSale
                                    v-if="!loading"
                                    :result-products="resultProducts"
                                    @add-product="addCarrito"
                                    @page-change="handlePageChange"
                                    :result-products-paginate="pagination"
                                />
                                <div v-else class="text-center text-sm font-semibold">
                                    <LoadingTable v-if="loading" :headers="11" :row-count="5" />
                                </div>
                            </div>
                        </div>
                    </ResizablePanel>

                    <ResizableHandle with-handle />

                    <!-- Carrito -->
                    <ResizablePanel id="panel-carrito" :default-size="33">
                        <div class="h-full overflow-auto rounded-lg p-4 shadow">
                            <div class="text-center text-sm font-semibold">
                                <TableCartSale :result-products="cardProducts" @remove-product="removeProducto" />
                            </div>
                        </div>
                    </ResizablePanel>

                    <ResizableHandle with-handle />

                    <!-- Cliente, Doctor, Tipo de Pago -->
                    <ResizablePanel id="panel-formularios" :default-size="10">
                        <div class="grid h-full grid-cols-1 gap-3 rounded-lg p-4 shadow md:grid-cols-5">
                            <div class="h-full overflow-auto rounded-lg p-4 shadow">
                                <div class="text-center text-sm font-semibold">
                                    <TypeVoucher @emit_type_voucher="getTypeVoucher" :reset="reset" />
                                </div>
                            </div>
                            <div class="h-full overflow-auto rounded-lg shadow">
                                <div class="text-center text-sm font-semibold">
                                    <CustomerCombobox @emit_customer="getCustomer" :reset="reset" />
                                </div>
                            </div>
                            <div class="h-full overflow-auto rounded-lg shadow">
                                <div class="text-center text-sm font-semibold">
                                    <DoctorCombobox @emit_doctor="getDoctor" :reset="reset" />
                                </div>
                            </div>
                            <div class="h-full overflow-auto rounded-lg p-4 shadow">
                                <div class="text-center text-sm font-semibold">
                                    <TypePaymentSale @emit_type_payment="getTypePayment" :reset="reset" />
                                </div>
                            </div>
                            <div class="h-full overflow-auto rounded-lg p-4 shadow">
                                <div class="text-center text-sm font-semibold">
                                    <CreateSale
                                        :card-products="cardProducts"
                                        :customer-data="customerData"
                                        :doctor-data="doctorData"
                                        :type-payment-data="typePaymentData"
                                        :type-voucher-data="typeVoucherData"
                                        @sale-processed="storeVenta"
                                    />
                                </div>
                            </div>
                        </div>
                    </ResizablePanel>
                </ResizablePanelGroup>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import CustomerCombobox from '@/components/Combobox/customerCombobox.vue';
import DoctorCombobox from '@/components/Combobox/doctorCombobox.vue';
import LoadingTable from '@/components/loadingTable.vue';
import { ResizableHandle, ResizablePanel, ResizablePanelGroup } from '@/components/ui/resizable';
import { useSale } from '@/composables/UseSale';
import { ComboBoxCustomer, TypePaymens } from '@/interface/ComboBox';
import { ProductLocalPrice } from '@/interface/ProductLocalPrice';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import CreateSale from './components/createSale.vue';
import SearchSale from './components/SearchSale.vue';
import TableCartSale from './components/TableCartSale.vue';
import TableResultSale from './components/TableResultSale.vue';
import TypePaymentSale from './components/TypePaymentSale.vue';
import TypeVoucher from './components/TypeVoucher.vue';
import { StoreSaleRequest } from './interface/Sale';

const {
    loadingResultProducts,
    resultProducts,
    addCardProduct,
    cardProducts,
    pagination,
    loading,
    deleteResultProduct,
    removeCardProduct,
    setCustomerData,
    setDoctorData,
    setTypePaymentData,
    customerData,
    doctorData,
    typePaymentData,
    typeVoucherData,
    setTypeVoucherData,
    storeSale,
    reset,
} = useSale();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Ventas',
        href: '/panel/ventas',
    },
];

const deleteResult = () => {
    deleteResultProduct();
};

const handlePageChange = (page: number) => {
    loadingResultProducts(page);
};

const searchFather = (text: string) => {
    loadingResultProducts(1, text);
};

const addCarrito = (product: ProductLocalPrice) => {
    addCardProduct(product);
};

const removeProducto = (product: ProductLocalPrice) => {
    console.log('removeProducto', product);
    removeCardProduct(product);
};

const getCustomer = (customer: ComboBoxCustomer | null) => {
    if (customer) {
        console.log('getIdCustomer2', customer);
        setCustomerData(customer);
    }
};

const getDoctor = (doctor: import('@/interface/ComboBox').comboBoxDoctor | null) => {
    console.log('getIdDoctor', doctor);
    if (doctor) {
        setDoctorData(doctor);
    }
};

const getTypePayment = (typePayment: TypePaymens | null) => {
    console.log('getIdTypePayment', typePayment);
    if (typePayment) {
        setTypePaymentData(typePayment);
    }
};
const getTypeVoucher = (typeVoucher: import('@/interface/ComboBox').TypeVoucher | null) => {
    console.log('getIdTypeVoucher', typeVoucher);
    if (typeVoucher) {
        setTypeVoucherData(typeVoucher);
    }
};

const storeVenta = (data: StoreSaleRequest) => {
    console.log('storeVenta', data);
    storeSale(data);
};
</script>

<style scoped>
/* Puedes agregar estilos adicionales si lo deseas */
</style>
