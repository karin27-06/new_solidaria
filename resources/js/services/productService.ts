import { InputLaboratoryResponse } from '@/interface/Inputs';
import { InputCategoryResponse } from '@/interface/Inputs';
import {
    ProductDeleteResponse,
    ProductRequest,
    ProductRequestUpdate,
    ProductResponse,
    showProductResponse,
} from '@/pages/panel/product/interface/Product';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

export interface ProductMovementRequest {
    product_id: number;
    boxes: number;
    fractions: number;
    type: string;
    lot: string;
    expiry_date: string;
    unit_price: number;
    total_price: number;
    movement_id: number;
}

export interface ProductMovementResponse {
    success: boolean;
    message: string;
    data: {
        id: number;
        product_id: number;
        quantity: number;
        fraction_quantity: number;
        total_price: number;
        unit_price: number;
        batch: string;
        expiry_date: string;
    };
}

export const ProductServices = {
    // list products
    async index(page: number, name: string): Promise<ProductResponse> {
        const response = await axios.get(`/panel/listar-products?page=${page}&name=${encodeURIComponent(name)}`);
        return response.data;
    },
    // inertia
    async store(data: ProductRequest) {
        router.post(route('panel.products.store'), data);
    },
    // show product
    async show(id: number): Promise<showProductResponse> {
        const response = await axios.get(`/panel/products/${id}`);
        return response.data;
    },
    // update product
    async update(id: number, data: ProductRequestUpdate): Promise<showProductResponse> {
        const response = await axios.put(`/panel/products/${id}`, data);
        return response.data;
    },
    async destroy(id: number): Promise<ProductDeleteResponse> {
        return await axios.delete(`/panel/products/${id}`);
    },
    // get Laboratory
    async getLaboratory(): Promise<InputLaboratoryResponse> {
        return await axios.get('/panel/inputs/laboratory_list');
    },
    // get Category
    async getCategory(): Promise<InputCategoryResponse> {
        return await axios.get('/panel/inputs/category_list');
    },
    async getProducts(search: string = ''): Promise<ProductResponse> {
        const response = await axios.get(`/panel/inputs/product_list?${search ? `?search=${encodeURIComponent(search)}` : ''}`);
        return response.data;
    },
    // Add product movement
    async storeProductMovement(data: ProductMovementRequest): Promise<ProductMovementResponse> {
        const response = await axios.post('/panel/product-movements', {
            product_id: data.product_id,
            quantity: data.boxes,
            fraction_quantity: data.fractions,
            total_price: data.total_price,
            unit_price: data.unit_price,
            batch: data.lot,
            expiry_date: data.expiry_date,
            quantity_type: data.type === 'Caja' ? 1 : data.type === 'Fracción' ? 0 : 2, // 2 for 'Ambas'
            movement_id: data.movement_id,
        });
        return response.data;
    },
};