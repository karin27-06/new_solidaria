import axios from 'axios';

export interface ProductMovementRequest {
    product_id: number;
    boxes: number;
    fractions: number;
    type: string;
    batch: string;
    expiry_date: string;
    unit_price: number;
    total_price: number;
    movement_id: number;
}

export interface ProductMovement {
    success: boolean;
    message: string;
    data: {
        id: number;
        productId: number;
        quantity: number;
        fractionQuantity: number;
        unitPrice: string;
        unitPriceEx: string;
        fractionPrice: string;
        totalPrice: string;
        labName: string;
        productName: string;
        unitPrices: string;
        batch: string;
        expiryDate: string;
        expiryDateDisplay: string;
        movementId: number;
        quantityStatus: number;
        quantityType: string;
        totalQuantity: string;
        generalPrice: string;
        status: number;
    };
}

export interface ProductMovementResponse {
    success: boolean;
    message: string;
    data: ProductMovement['data'][];
    subtotal: string;
    tax: string;
    total: string;
}

export interface ProductMovementDeleteResponse {
    success: boolean;
    message: string;
    error?: string;
}

export const ProductMovementServices = {
    async storeProductMovement(data: ProductMovementRequest): Promise<ProductMovementResponse> {
        const response = await axios.post('/panel/product-movements', {
            product_id: data.product_id,
            quantity: data.boxes,
            fraction_quantity: data.fractions,
            total_price: data.total_price,
            unit_price: data.unit_price,
            batch: data.batch,
            expiry_date: data.expiry_date,
            quantity_type: data.type === 'Box' ? 1 : data.type === 'Fraction' ? 0 : 2,
            movement_id: data.movement_id,
        });
        return response.data;
    },

    async updateProductMovement(id: number, data: ProductMovementRequest): Promise<ProductMovementResponse> {
        try {
            const response = await axios.put(`/panel/product-movements/${id}`, {
                product_id: data.product_id,
                quantity: data.boxes,
                fraction_quantity: data.fractions,
                total_price: data.total_price,
                unit_price: data.unit_price,
                batch: data.batch,
                expiry_date: data.expiry_date,
                quantity_type: data.type === 'Box' ? 1 : data.type === 'Fraction' ? 0 : 2,
                movement_id: data.movement_id,
            });
            return response.data;
        } catch (error) {
            console.error('Error updating product movement:', error);
            throw error;
        }
    },

    async getProductMovements(movementId: number): Promise<ProductMovementResponse> {
        try {
            const response = await axios.get('/panel/listar-product-movements', {
                params: { movementId }
            });
            return response.data;
        } catch (error) {
            console.error('Error fetching product movements:', error);
            throw error;
        }
    },

    async deleteProductMovement(id: number): Promise<ProductMovementDeleteResponse> {
        if (!id) {
            throw new Error('El ID del movimiento de producto es requerido');
        }
        try {
            const response = await axios.delete<ProductMovementDeleteResponse>('/panel/product-movements/delete', {
                data: {
                    id: id,
                },
            });
            return response.data;
        } catch (error: any) {
            console.error('Error al eliminar el movimiento de producto:', error.response?.data || error.message);
            throw new Error(error.response?.data?.message || 'Error al eliminar el movimiento de producto');
        }
    }
};