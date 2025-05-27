import { ComboBoxCustomer, comboBoxDoctor, TypePaymens, TypeVoucher } from '@/interface/ComboBox';
import { Pagination } from '@/interface/paginacion';
import { ProductLocalPrice } from '@/interface/ProductLocalPrice';
import { saleServices } from '@/services/saleServices';
import { showSuccessMessage } from '@/utils/message';
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
    };
};
