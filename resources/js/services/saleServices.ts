import { ProductLocalPriceResponse } from '@/interface/ProductLocalPrice';
import axios from 'axios';
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
};
