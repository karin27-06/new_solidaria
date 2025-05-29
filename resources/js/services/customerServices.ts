import { InputClient_typeResponse } from '@/interface/Inputs';
import {
    CustomerDeleteResponse,
    CustomerRequest,
    CustomerResponse,
    showCustomerResponse,
} from '@/pages/panel/customer/interface/Customer';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

export const CustomerServices = {
    //List customer
    async index(page: number, name: string): Promise<CustomerResponse> {
        const response = await axios.get(`/panel/listar-customers?page=${page}&name=${encodeURIComponent(name)}`);
        return response.data;
    },

    //Inertia
    async store(data: CustomerRequest) {
        router.post(route('panel.customers.store'), data);
    },

    //Show customer
    async show(id: number): Promise<showCustomerResponse> {
        const response = await axios.get(`/panel/customers/${id}`);
        return response.data;
    },

    //Update customer
    async update(id: number, data: CustomerRequest): Promise<showCustomerResponse> {
        const response = await axios.put(`/panel/customers/${id}`, data);
        return response.data;
    },

    //Delete customer
    async destroy(id: number): Promise<CustomerDeleteResponse> {
        const response = await axios.delete(`/panel/customers/${id}`);
        return response.data;
    },
        // get Client_type
        async getClient_type(): Promise<InputClient_typeResponse> {
            return await axios.get('/panel/inputs/client_type_list');
        },
};