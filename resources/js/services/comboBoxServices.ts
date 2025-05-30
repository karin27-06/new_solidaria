import { ComboBoxCustomer, comboBoxDoctor, TypePaymens } from '@/interface/ComboBox';
import axios from 'axios';

export const comboBoxServices = {
    // get data customer

    async getCustomer(search: string): Promise<ComboBoxCustomer[]> {
        const response = await axios.get(`/panel/combobox/customer?search=${search}`);
        return response.data;
    },
    // get data doctor
    async getDoctor(search: string): Promise<comboBoxDoctor[]> {
        const response = await axios.get(`/panel/combobox/doctor?search=${search}`);
        return response.data;
    },

    // get data type payment
    async getTypePayment(): Promise<TypePaymens[]> {
        const response = await axios.get('/panel/inputs/type-payments');
        return response.data;
    },
};
