import { InputClient_type } from '@/interface/Inputs';
import { Pagination } from '@/interface/paginacion';
import { CustomerRequest, CustomerRequestUpdate, CustomerResource } from '@/pages/panel/customer/interface/Customer';
import { CustomerServices } from '@/services/customerServices';
import { showSuccessMessage } from '@/utils/message';
import { reactive } from 'vue';

export const useCustomer = () => {
    const principal = reactive<{
        customerList: CustomerResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idCustomer: number;
        statusModal: {
            update: boolean;
            delete: boolean;
        };
        customerData: CustomerResource;
        client_typeList: InputClient_type[];
    }>({
        customerList: [],
        paginacion: {
            total: 0,
            current_page: 0,
            per_page: 0,
            last_page: 0,
            from: 0,
            to: 0,
        },
        loading: false,
        filter: '',
        idCustomer: 0,
        statusModal: {
            update: false,
            delete: false,
        },
        customerData: {
            id: 0,
            code: '',
            firstname: '',
            lastname: '',
            address: '',
            phone: '',
            birthdate: '',
            client_type_id: 0,
            client_type: '',
        },
        client_typeList: [],
    });

    // reset customer data
    const resetCustomerData = () => {
        principal.customerData = {
            id: 0,
            code: '',
            firstname: '',
            lastname: '',
            address: '',
            phone: '',
            birthdate: '',
            client_type_id: 0,
            client_type: '',
        };
    };

    // loading Customers
    const loadingCustomers = async (page: number = 1, name: string = '') => {
        principal.loading = true;
        try {
            const response = await CustomerServices.index(page, name);
            principal.customerList = response.customers;
            principal.paginacion = response.pagination;
            console.log(response);
            if (principal.client_typeList.length === 0 && principal.paginacion.current_page === 1) {
                const client_typeResponse = await CustomerServices.getClient_type();
                principal.client_typeList = client_typeResponse.data;
                console.log('envie la peticion');
            }

        } catch (error) {
            console.error(error);
        } finally {
            principal.loading = false;
        }
    };

    // creating customer
    const createCustomer = async (data: CustomerRequest) => {
        try {
            await CustomerServices.store(data);
        } catch (error) {
            console.error(error);
        }
    };

    // get customer by id
    const getCustomerById = async (id: number) => {
        try {
            if (id === 0) {
                resetCustomerData();
                return;
            }
            const response = await CustomerServices.show(id);
            if (response) {
                principal.customerData = response.customer;
                console.log(principal.customerData.code);
                principal.idCustomer = response.customer.id;
                if (principal.client_typeList.length === 0) {
                    const client_typeResponse = await CustomerServices.getClient_type();
                    principal.client_typeList = client_typeResponse.data;
                    console.log('envie la peticion');
                }
                principal.statusModal.update = true;
            }
        } catch (error) {
            console.error(error);
        }
    };

    // update customer
    const updateCustomer = async (id: number, data: CustomerRequestUpdate) => {
        try {
            const response = await CustomerServices.update(id, data);
            if (response) {
                showSuccessMessage('Cliente actualizado', 'El Cliente se actualizó correctamente');
                principal.statusModal.update = false;
                loadingCustomers(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.client_typeList = [];
        }
    };

    // delete customer
    const deleteCustomer = async (id: number) => {
        try {
            const response = await CustomerServices.destroy(id);
            if (response) {
                showSuccessMessage('Cliente eliminado', 'El cliente se eliminó correctamente');
                principal.statusModal.delete = false;
                loadingCustomers(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.statusModal.delete = false;
        }
    };

    return {
        principal,
        loadingCustomers,
        createCustomer,
        getCustomerById,
        resetCustomerData,
        updateCustomer,
        deleteCustomer,
    };
};
