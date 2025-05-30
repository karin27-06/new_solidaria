import { Pagination } from '@/interface/paginacion';

export interface ProductSaleRequest {
    product_local_id: number;
    quantity_box: number;
    quantity_fraction: number;
    price_box: number;
    price_fraction: number;
}

export interface StoreSaleRequest {
    customer_id?: number | null;
    doctor_id?: number | null;
    type_voucher_id: number;
    type_payment_id: number;
    code_card?: string | null;
    op_gravada: number;
    op_exonerada?: number | null;
    op_inafecta?: number | null;
    igv: number;
    total: number;
    status_sale?: boolean | null;
    state_sunat?: boolean | null;
    products: ProductSaleRequest[];
}

export interface SaleResponseStore {
    status: boolean;
    message: string;
}

export interface SaleResource {
    id: number;
    usuario: string;
    cliente: string;
    local: string;
    doctor: string;
    tipo_comprobante: string;
    tipo_pago: string;
    codigo: string;
    codigo_tarjeta: string;
    op_gravada: number;
    op_exonerada: number;
    op_inafecta: number;
    igv: number;
    total: number;
    estado_venta: boolean;
    estado_sunat: boolean;
    created_at: string;
}

export interface SaleListResponse {
    sales: SaleResource[];
    pagination: Pagination;
}
