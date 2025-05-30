import { Pagination } from '@/interface/paginacion';
import { getPermissionList, PermissionResource, PermissionUpdateRequest } from '@/pages/panel/permission/interface/Permission';
import { PermissionServices } from '@/services/permissionServices';
import { showSuccessMessage } from '@/utils/message';
import { reactive, ref } from 'vue';
import { toast } from 'vue-sonner'; // ✅ Importado

export const usePermission = () => {
    const principal = reactive<{
        permissionList: PermissionResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idPermission: number;
        statusModal: {
            update: boolean;
            delete: boolean;
        };
        permissionData: PermissionResource;
    }>({
        permissionList: [],
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
        idPermission: 0,
        statusModal: {
            update: false,
            delete: false,
        },
        permissionData: {
            id: 0,
            name: '',
            created_at: '',
            updated_at: '',
        },
    });

    const resetPermissionData = () => {
        principal.permissionData = {
            id: 0,
            name: '',
            created_at: '',
            updated_at: '',
        };
    };

    const permissions = ref<getPermissionList[]>([]);

    const loadingPermissions = async (page: number = 1, name: string = '') => {
        principal.loading = true;
        try {
            const response = await PermissionServices.index(page, name);
            principal.permissionList = response.permissions;
            principal.paginacion = response.pagination;
        } catch (error) {
            console.error(error);
        } finally {
            principal.loading = false;
        }
    };

    const createPermission = async (data: { name: string }) => {
        try {
            await PermissionServices.store(data);
        } catch (error) {
            console.error(error);
        }
    };

    const getPermissionById = async (id: number) => {
        try {
            if (id === 0) {
                resetPermissionData();
                return;
            }
            const response = await PermissionServices.show(id);
            if (response.status) {
                principal.permissionData = response.permission;
                principal.idPermission = response.permission.id;
                principal.statusModal.update = true;
            }
        } catch (error) {
            console.error(error);
        }
    };

    const updatePermission = async (id: number, data: PermissionUpdateRequest) => {
        try {
            const response = await PermissionServices.update(id, data);
            if (response.status) {
                showSuccessMessage('El permiso se actualizó correctamente', 'Permiso actualizado');
                principal.statusModal.update = false;
                loadingPermissions(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        }
    };

    const deletePermission = async (id: number) => {
        try {
            const deletePromise = PermissionServices.destroy(id);
            toast.promise(deletePromise, {
                loading: 'Eliminando permiso...',
                success: () => {
                    principal.statusModal.delete = false;
                    loadingPermissions(principal.paginacion.current_page, principal.filter);
                    return {
                        message: 'Permiso eliminado',
                        description: 'El permiso se eliminó correctamente',
                    };
                },
                error: () => {
                    principal.statusModal.delete = false;
                    return {
                        message: 'Error al eliminar',
                        description: 'Hubo un problema al eliminar el permiso',
                    };
                },
            });
        } catch (error) {
            console.error(error);
            toast.error('Error inesperado', {
                description: 'No se pudo eliminar el permiso.',
            });
        } finally {
            principal.statusModal.delete = false;
        }
    };

    const getPermissions = async () => {
        try {
            const response = await PermissionServices.getPermissions();
            permissions.value = response;
        } catch (error) {
            console.error(error);
        }
    };

    return {
        principal,
        permissions,
        loadingPermissions,
        createPermission,
        getPermissionById,
        resetPermissionData,
        updatePermission,
        deletePermission,
        getPermissions,
    };
};
