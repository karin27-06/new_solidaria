<template>
    <Dialog :open="modal" @update:open="clouseModal">
        <DialogContent class="w-full max-w-4xl">
            <DialogHeader>
                <DialogTitle>Editar Cliente</DialogTitle>
                <DialogDescription>
                    <p class="text-sm text-muted-foreground">Edita los datos del cliente</p>
                </DialogDescription>
            </DialogHeader>
            <form @submit="onSubmit" class="flex flex-col gap-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6"> <!-- Grilla para dos columnas -->
                    <FormField v-slot="{ componentField }" name="code">
                        <FormItem>
                            <FormLabel>Codigo</FormLabel>
                            <FormControl>
                                <Input id="code" type="text" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                    <FormField v-slot="{ componentField }" name="firstname">
                        <FormItem>
                            <FormLabel>Nombre</FormLabel>
                            <FormControl>
                                <Input id="firstname" type="text" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                    <FormField v-slot="{ componentField }" name="lastname">
                        <FormItem>
                            <FormLabel>Apellido</FormLabel>
                            <FormControl>
                                <Input id="lastname" type="text" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                        <FormField v-slot="{ componentField }" name="address">
                        <FormItem>
                            <FormLabel>Dirección</FormLabel>
                            <FormControl>
                                <Input id="address" type="text" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                        <FormField v-slot="{ componentField }" name="phone">
                        <FormItem>
                            <FormLabel>Telefono</FormLabel>
                            <FormControl>
                                <Input id="phone" type="text" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                        <FormField v-slot="{ componentField }" name="birthdate">
                        <FormItem>
                            <FormLabel>Cumpleaños</FormLabel>
                            <FormControl>
                                <Input id="birthdate" type="text" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                    <FormField v-slot="{ componentField }" name="client_type_id">
                        <FormItem>
                            <FormLabel>Tipo de cliente</FormLabel>
                            <FormControl>
                                <Select v-bind="componentField">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecciona el tipo de cliente" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Tipo de Cliente</SelectLabel>
                                            <SelectItem v-for="type in props.client_type" :key="type.id" :value="type.id">
                                                {{ type.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div> 
                <DialogFooter class="flex justify-end gap-2">
                    <Button type="submit">Guardar Cambios</Button>
                    <Button type="button" variant="outline" @click="clouseModal">Cancelar</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { InputClient_type} from '@/interface/Inputs';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm} from 'vee-validate';
import { watch } from 'vue';
import { z } from 'zod';
import { CustomerRequestUpdate, CustomerResource } from '../interface/Customer';

const props = defineProps<{
    modal: boolean;
    customerData: CustomerResource;
    client_type: InputClient_type[];
}>();



const emit = defineEmits<{
    (e: 'closeModal', open: boolean): void;
    (e: 'updateCustomer', customerData: CustomerRequestUpdate, customer_id: number): void;
}>();

const clouseModal = () => {
    emit('closeModal', false);
};

const formShema = toTypedSchema(
    z.object({
        code: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'codigo mayor a 2 letras' })
            .max(11, { message: 'codigo menor a 11 letras' }),
        firstname: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'nombre mayor a 2 letras' })
            .max(50, { message: 'nombre menor a 50 letras' }),
        lastname: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'apellido mayor a 2 letras' })
            .max(50, { message: 'apellido menor a 50 letras' }),
        address: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'dirección mayor a 2 letras' })
            .max(200, { message: 'dirección menor a 200 letras' }),
        phone: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'telefono mayor a 2 letras' })
            .max(9, { message: 'telefóno menor a 10 letras' }),
        birthdate: z.string(),
        client_type_id: z.number({ message: 'campo obligatorio' }),
            }),
);

const { handleSubmit, setValues} = useForm({
    validationSchema: formShema,
    initialValues: {
        code: props.customerData.code,
        firstname: props.customerData.firstname,
        lastname: props.customerData.lastname,
        address: props.customerData.address,
        phone: props.customerData.phone,
        birthdate: props.customerData.birthdate,
        client_type_id: props.customerData.client_type_id,
    },
});


watch(
    () => props.customerData,
    (newData) => {
        if (newData) {
            setValues({
    code: newData.code,
    firstname: newData.firstname,
    lastname: newData.lastname,
    address: newData.address,
    phone: newData.phone, 
    birthdate: newData.birthdate,
    client_type_id: newData.client_type_id,
            });
        }
    },
    
    { deep: true, immediate: true },
);


const onSubmit = handleSubmit((values) => {
    const updatedCustomer: CustomerRequestUpdate = {
        code: values.code,
        firstname: values.firstname,
        lastname: values.lastname,
        address: values.address,
        phone: values.phone,
        birthdate: values.birthdate,
        client_type_id: values.client_type_id,
    };
    emit('updateCustomer', updatedCustomer, props.customerData.id);
    clouseModal();
});
</script>
<style scoped></style>
