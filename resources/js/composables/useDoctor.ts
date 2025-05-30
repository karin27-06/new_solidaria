import { Pagination } from '@/interface/paginacion';
import { DoctorResource, storeDoctorRequest, updateDoctorRequest } from '@/pages/panel/doctor/interface/Doctor';
import { DoctorServices } from '@/services/doctorServices';
import { showSuccessMessage } from '@/utils/message';
import { reactive } from 'vue';
import { toast } from 'vue-sonner';
 
export const useDoctors = () => {
    const principal = reactive<{
        doctorList: DoctorResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idDoctor: number;
        statusModal: {
            update: boolean;
            delete: boolean;
        };
        doctorData: DoctorResource;
    }>({
        doctorList: [],
        paginacion: {
            total: 0,
            current_page: 0,
            per_page: 0,
            last_page: 0,
            from: 0,
            to: 0,
        },
        loading: false,
        filter: '',
        idDoctor: 0,
        statusModal: {
            update: false,
            delete: false,
        },
        doctorData: {
            id: 0,
            name: '',
            code: '',
            start_date: '',
            state: true,
            created_at: '',
        },
    });

    // loading doctors
    const loadingDoctors = async (page: number = 1, name: string = '', status: boolean = true) => {
        if (status) {
            principal.loading = true;
            try {
                const response = await DoctorServices.index(page, name);
                principal.doctorList = response.doctors;
                principal.paginacion = response.pagination;
            } catch (error) {
                console.error(error);
            } finally {
                principal.loading = false;
            }
        }
    };
    // creating doctor

    const createDoctor = async (data: storeDoctorRequest) => {
        try {
            await DoctorServices.store(data);
        } catch (error) {
            console.error(error);
        }
    };

    //  getting doctor
    const getDoctor = async (id: number) => {
        try {
            if (id === 0) {
                principal.doctorData = {
                    id: 0,
                    name: '',
                    code: '',
                    start_date: '',
                    state: true,
                    created_at: '',
                };
                return;
            }
            const response = await DoctorServices.show(id);
            if (response.status === true) {
                principal.doctorData = response.doctor;
                principal.idDoctor = response.doctor.id;
                principal.statusModal.update = true;
            }
        } catch (error) {
            console.error(error);
        }
    };
    // updating doctor
    const updateDoctor = async (id: number, data: updateDoctorRequest) => {
        try {
            const response = await DoctorServices.update(id, data);
            if (response.status === true) {
                showSuccessMessage('El doctor se actualizo correctamente', 'Doctor actualizado');
                loadingDoctors(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.statusModal.update = false;
        }
    };
    // Deleting doctor con toast.promise (sin cambiar estructura)
const deleteDoctor = async (id: number) => {
  try {
    const deletePromise = DoctorServices.destroy(id);

    toast.promise(deletePromise, {
      loading: 'Eliminando doctor...',
      success: () => {
        principal.statusModal.delete = false;
        loadingDoctors(principal.paginacion.current_page, principal.filter);
        return {
          message: 'Doctor eliminado',
          description: 'El doctor se eliminó correctamente.',
        };
      },
      error: () => {
        principal.statusModal.delete = false;
        return {
          message: 'Error al eliminar',
          description: 'No se pudo eliminar el doctor.',
        };
      },
    });

  } catch (error) {
    console.error(error);
  } finally {
    principal.statusModal.delete = false;
  }
};

    return {
        principal,
        loadingDoctors,
        createDoctor,
        getDoctor,
        updateDoctor,
        deleteDoctor,
    };
};
