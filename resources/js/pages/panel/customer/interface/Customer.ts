import { Pagination } from '@/interface/paginacion';

export type CustomerResource = {
    id: number;
    code: string;
    firstname: string;
    lastname: string;
    address: string;
    phone: string;
    birthdate: string;
    client_type_id: number;
    client_type: string;
};

export type CustomerRequest = {
    code: string;
    firstname: string;
    lastname: string;
    address: string;
    phone: string;
    birthdate: string;
    client_type_id: number;
};

export type CustomerRequestUpdate = {
    code: string;
    firstname: string;
    lastname: string;
    address: string;
    phone: string;
    birthdate: string;
    client_type_id: number;
};

export type showCustomerResponse = {
    message: string;
    customer: CustomerResource;
};

export type CustomerDeleteResponse = {
    message: string;
};

export type CustomerResponse = {
    customers: CustomerResource[];
    pagination: Pagination;
};
