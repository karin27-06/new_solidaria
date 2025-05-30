import { Pagination } from '@/interface/paginacion';

export type MovementResource = {
    id: number;
    code: string;
    issue_date: string;
    credit_date: string;
    supplier_id: number;
    user_id: number;
    type_movement_id: number;
    status: number;
    statustext: string;
    igv_status: number;
    payment_type: string;
    created_at: string;
    updated_at: string;
    subtotal: string;
    igv: string;
    total: string;
    supplier?: {
        id: number;
        name: string;
    };
    user?: {
        id: number;
        name: string;
    };
    typemovement?: {
        id: number;
        name: string;
    };
};

export type MovementRequest = {
    code: string;
    issue_date: string;
    credit_date: string;
    supplier_id: number;
    user_id: number;
    type_movement_id: number;
    status: number;
    statustext: string;
    igv_status: number;
    subtotal: string;
    igv: string;
    total: string;
};

export type showMovementResponse = {
    state: boolean;
    message: string;
    movement: MovementResource;
};

export type MovementDeleteResponse = {
    state: boolean;
    message: string;
};

export type MovementUpdateRequest = {
    code?: string;
    issue_date?: string;
    credit_date?: string;
    supplier_id?: number;
    type_movement_id?: number;
    status?: number;
    statustext?: string;
    igv_status?: number;
    subtotal?: string;
    igv?: string;
    total?: string;
};

export type MovementResponse = {
    movements: MovementResource[];
    pagination: Pagination;
};