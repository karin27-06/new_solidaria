import { ProductLocalPriceResponse } from '@/interface/ProductLocalPrice';
import { SaleResponseStore, StoreSaleRequest } from '@/pages/panel/sale/interface/Sale';
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

export const saleServices = {
    async searchProductsLocalPrice(page: number, search: string = ''): Promise<ProductLocalPriceResponse> {
        const params = new URLSearchParams();
        params.append('page', page.toString());
        if (search) {
            params.append('search', search);
        }

        const response = await axios.get(`/api/search/product?${params.toString()}`);
        return response.data;
    },
    async storeSale(data: StoreSaleRequest): Promise<SaleResponseStore> {
        try {
            const response = await axios.post('/api/sales/pipeline', data);
            return response.data;
        } catch (error) {
            return handleApiError(error);
        }
    },
};
