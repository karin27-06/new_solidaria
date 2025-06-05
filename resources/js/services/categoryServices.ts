import {
    CategoryDeleteResponse,
    CategoryRequest,
    CategoryResponse,
    CategoryUpdateRequest,
    showCategoryResponse,
} from '@/pages/panel/category/interface/Category';
import { router } from '@inertiajs/vue3';
import axios, { AxiosError } from 'axios';

type ValidationError = {
    errors: Record<string, string[]>;
    message: string;
};

type ApiError = {
    message: string;
    status?: number;
};
const handleApiError = (error: unknown): never => {
    if (axios.isAxiosError(error)) {
        const axiosError = error as AxiosError;

        // Error de validación (422)
        if (axiosError.response?.status === 422) {
            const validationError = axiosError.response.data as ValidationError;
            const errorMessages = Object.entries(validationError.errors)
                .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
                .join('\n');

            throw new Error(errorMessages);
        }

        // Otros errores HTTP
        const apiError = axiosError.response?.data as ApiError;
        throw new Error(apiError?.message || axiosError.message);
    }

    // Errores no relacionados a Axios
    throw error instanceof Error ? error : new Error('Error desconocido');
};

export const CategoryServices = {
    //list categories
    async index(page: number, name: string): Promise<CategoryResponse> {
        const response = await axios.get(`/panel/listar-categories?page=${page}&name=${encodeURIComponent(name)}`);
        return response.data;
    },
    //inertia
    async store(data: CategoryRequest) {
        try {
            router.post(route('panel.categories.store'), data);
        } catch (error) {
            handleApiError(error);
        }
    },
    // show categories
    async show(id: number): Promise<showCategoryResponse> {
        const response = await axios.get(`categories/${id}`);
        return response.data;
    },
    // update categories
    async update(id: number, data: CategoryUpdateRequest): Promise<showCategoryResponse> {
        const response = await axios.put(`categories/${id}`, data);
        return response.data;
    },
    // detele categories
    async destroy(id: number): Promise<CategoryDeleteResponse> {
        const response = await axios.delete(`categories/${id}`);
        return response.data;
    },
};
