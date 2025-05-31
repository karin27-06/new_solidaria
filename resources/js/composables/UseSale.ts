import { ComboBoxCustomer, comboBoxDoctor, TypePaymens, TypeVoucher } from '@/interface/ComboBox';
import { Pagination } from '@/interface/paginacion';
import { ProductLocalPrice } from '@/interface/ProductLocalPrice';
import { SaleResource, StoreSaleRequest } from '@/pages/panel/sale/interface/Sale';
import { saleServices } from '@/services/saleServices';
import { showErrorMessage, showSuccessMessage } from '@/utils/message';
import { ref } from 'vue';

export const useSale = () => {
    const resultProducts = ref<ProductLocalPrice[]>([]);
    const pagination = ref<Pagination>({
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
    });
    const loading = ref(false);
    const loadingSale = ref(false);
    const salesList = ref<SaleResource[]>([]);
    const paginationSale = ref<Pagination>({
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
    });
    // DATA PARA EL BACKEND
    const cardProducts = ref<ProductLocalPrice[]>([]);
    const customerData = ref<ComboBoxCustomer>({
        id: 0,
        firstname: 'no seleccionado',
        lastname: 'no seleccionado',
    });
    const doctorData = ref<comboBoxDoctor>({
        id: 0,
        name: 'no seleccionado',
    });
    const typePaymentData = ref<TypePaymens>({
        id: 0,
        name: 'no seleccionado',
    });
    const typeVoucherData = ref({
        id: 0,
        name: 'no seleccionado',
    });

    const reset = ref<boolean>(false);

    const handleApiError = (error: unknown, defaultMessage = 'Error desconocido') => {
        console.error('API Error:', error);
        const errorMessage = error instanceof Error ? error.message : defaultMessage;
        showErrorMessage('Error', errorMessage);
    };

    const deleteResultProduct = () => {
        try {
            loading.value = true;
            resultProducts.value = [];
            pagination.value = {
                total: 0,
                current_page: 0,
                per_page: 0,
                last_page: 0,
                from: 0,
                to: 0,
            };
        } catch (error) {
            console.error('Error deleting product:', error);
        } finally {
            loading.value = false;
        }
    };

    const loadingResultProducts = async (page: number = 1, value: string = '') => {
        loading.value = true;
        reset.value = false;
        try {
            const response = await saleServices.searchProductsLocalPrice(page, value);
            resultProducts.value = response.products;
            pagination.value = response.pagination;
        } catch (error) {
            console.error('Error loading products:', error);
        } finally {
            loading.value = false;
        }
    };
    const addCardProduct = (product: ProductLocalPrice) => {
        const existingProduct = cardProducts.value.find((p) => p.id === product.id);
        if (existingProduct) {
            existingProduct.quantify_box += product.quantify_box;
            existingProduct.quantify_fraction += product.quantify_fraction;
        } else {
            cardProducts.value.push(product);
        }
        showSuccessMessage('exito', 'Producto agregado al carrito');
    };
    const removeCardProduct = (product: ProductLocalPrice) => {
        const index = cardProducts.value.findIndex((p) => p.id === product.id);
        if (index !== -1) {
            const existingProduct = cardProducts.value[index];
            existingProduct.quantify_box -= product.quantify_box;
            existingProduct.quantify_fraction -= product.quantify_fraction;
            if (existingProduct.quantify_box <= 0 && existingProduct.quantify_fraction <= 0) {
                cardProducts.value.splice(index, 1);
            }
        }
    };
    const setCustomerData = (customer: ComboBoxCustomer) => {
        customerData.value = customer;
    };
    const setDoctorData = (doctor: comboBoxDoctor) => {
        doctorData.value = doctor;
    };
    const setTypePaymentData = (typePayment: TypePaymens) => {
        typePaymentData.value = typePayment;
    };
    const setTypeVoucherData = (typeVoucher: TypeVoucher) => {
        typeVoucherData.value = typeVoucher;
    };

    const storeSale = async (data: StoreSaleRequest) => {
        loading.value = true;
        try {
            const response = await saleServices.storeSale(data);
            if (response.status) {
                showSuccessMessage('Éxito', response.message);
                deleteResultProduct();
                cardProducts.value = [];
                customerData.value = {
                    id: 0,
                    firstname: 'no seleccionado',
                    lastname: 'no seleccionado',
                };
                doctorData.value = {
                    id: 0,
                    name: 'no seleccionado',
                };
                typePaymentData.value = {
                    id: 0,
                    name: 'no seleccionado',
                };
                typeVoucherData.value = {
                    id: 0,
                    name: 'no seleccionado',
                };
            } else {
                console.error('Error al guardar la venta:', response.message);
            }
            reset.value = true;
        } catch (error) {
            handleApiError(error, 'Error al guardar la venta');
            console.error('Error al guardar la venta:', error);
        } finally {
            loading.value = false;
        }
    };

    const saleList = async (page: number = 1, search: string = '') => {
        loadingSale.value = true;
        try {
            const response = await saleServices.saleList(page, search);
            salesList.value = response.sales;
            paginationSale.value = response.pagination;
        } catch (error) {
            handleApiError(error, 'Error al cargar la lista de ventas');
            console.error('Error al cargar la lista de ventas:', error);
        } finally {
            loadingSale.value = false;
        }
    };

    return {
        loadingResultProducts,
        deleteResultProduct,
        resultProducts,
        pagination,
        loading,
        addCardProduct,
        removeCardProduct,
        cardProducts,
        customerData,
        doctorData,
        typePaymentData,
        typeVoucherData,
        setCustomerData,
        setDoctorData,
        setTypePaymentData,
        setTypeVoucherData,
        storeSale,
        reset,
        loadingSale,
        salesList,
        paginationSale,
        saleList,
    };
};
