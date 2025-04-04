import { Pagination } from '@/interface/paginacion';

export type DoctorResource = {
    id: number;
    name: string;
    code: string;
    start_date: string;
    state: boolean;
    created_at: Date | null;
};

export type storeDoctorRequest = {
    name: string;
    code: string;
    start_date: string;
};

export type updateDoctorRequest = {
    name: string;
    code: string;
    start_date: string;
    state: boolean;
};

export type showDoctorResponse = {
    status: boolean;
    message: string;
    doctor: DoctorResource;
};

export type DoctorDeleteResponse = {
    status: boolean;
    message: string;
};
export type DoctorResponse = {
    doctors: DoctorResource[];
    pagination: Pagination;
};
