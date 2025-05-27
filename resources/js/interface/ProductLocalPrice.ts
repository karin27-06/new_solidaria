import { Pagination } from './paginacion';

export interface ProductLocalPrice {
    id: number;
    product_id: number;
    product_name: string;
    product_composition: string;
    states_fraction: boolean;
    state_igv: boolean;
    fraction: number;
    stockBox: number;
    stockFraction: number;
    unit_price: number;
    fraction_price: number;
    quantify_box: number;
    quantify_fraction: number;
}

export interface ProductLocalPriceResponse {
    products: ProductLocalPrice[];
    pagination: Pagination;
}
