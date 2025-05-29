<template>
    <div class="container-table">
        <LoadingTable v-if="loading" :headers="6" :row-count="10" />
        <div v-else class="table-content">
            <div class="table-container">
                <Table class="table-responsive">
                    <TableHeader class="table-header-row">
                        <TableRow class="table-header-row">
                            <TableHead class="table-head-id">ID</TableHead>
                            <TableHead class="table-head">CÓDIGO</TableHead>
                            <TableHead class="table-head">NOMBRES</TableHead>
                            <TableHead class="table-head">APELLIDOS</TableHead>
                            <TableHead class="table-head">DIRECCIÓN</TableHead>
                            <TableHead class="table-head">TELEFÓNO</TableHead>
                            <TableHead class="table-head">CUMPLEAÑOS</TableHead>
                            <TableHead class="table-head">TIPO DE CLIENTE</TableHead>
                            <TableHead class="table-head-actions">ACCIONES</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody class="table-body">
                        <TableRow v-for="customer in customerList" :key="customer.id" class="table-row">
                            <td class="cell-id">{{ customer.id }}</td>
                            <td class="cell-data">{{ customer.code }}</td>
                            <td class="cell-data">{{ customer.firstname }}</td>
                            <td class="cell-data">{{ customer.lastname }}</td>
                            <td class="cell-data">{{ customer.address }}</td>
                            <td class="cell-data">{{ customer.phone }}</td>
                            <td class="cell-data">{{ customer.birthdate }}</td>
                            <td class="cell-data">{{ customer.client_type}}</td>
                            <td class="cell-actions">
                                <div class="actions-container">
                                    <Button variant="ghost" size="sm" class="action-button" @click="openModal(customer.id)" title="Editar Cliente">
                                        <UserPen class="action-icon" />
                                        <span class="sr-only">Editar cliente</span>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="action-button"
                                        @click="openModalDelete(customer.id)"
                                        title="Eliminar Cliente"
                                    >
                                        <Trash class="action-icon" />
                                        <span class="sr-only">Eliminar cliente</span>
                                    </Button>
                                </div>
                            </td>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <PaginationCustomer :meta="customerPaginate" @page-change="$emit('page-change', $event)" class="mt-6" />
        </div>
    </div>
</template>

<script setup lang="ts">
import LoadingTable from '@/components/loadingTable.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useToast } from '@/components/ui/toast';
import { Pagination } from '@/interface/paginacion';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Trash, UserPen } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import PaginationCustomer from '../../../../components/pagination.vue';
import { CustomerResource } from '../interface/Customer';

const { toast } = useToast();
const emit = defineEmits<{
    (e: 'page-change', page: number): void;
    (e: 'open-modal', id_customer: number): void;
    (e: 'open-modal-delete', id_customer: number): void;
}>();

const page = usePage<SharedData>();

const message = ref(page.props.flash?.message || '');
onMounted(() => {
    if (message.value) {
        toast({
            title: 'Notificación',
            description: message.value,
        });
    }
});

const { customerList, customerPaginate } = defineProps<{
    customerList: CustomerResource[];
    customerPaginate: Pagination;
    loading: boolean;
}>();

const openModal = (id: number) => {
    emit('open-modal', id);
};

const openModalDelete = (id: number) => {
    emit('open-modal-delete', id);
};
</script>
<style scoped lang="css"></style>
