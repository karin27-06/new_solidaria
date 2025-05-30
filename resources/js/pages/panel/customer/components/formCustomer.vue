<template>
    <Head title="Nuevo Cliente"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Alert v-if="hasErrors" variant="destructive">
                <AlertCircle class="h-4 w-4"></AlertCircle>
                <AlertTitle>Error</AlertTitle>
                <AlertDescription>
                    <ul class="mt-2 list-inside list-disc text-sm">
                        <li v-for="(message, field) in page.props.errors" :key="field">
                            {{ message }}
                        </li>
                    </ul>
                </AlertDescription>
            </Alert>
            <Card class="mt-4 flex flex-col gap-4">
                <CardHeader>
                    <CardTitle>NUEVO CLIENTE</CardTitle>
                    <CardDescription>Complete los campos para crear un nuevo cliente</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit="onSubmit" class="flex flex-col gap-6">
                        <FormField v-slot="{ componentField }" name="code">
                            <FormItem>
                                <FormLabel>Código</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="codigo" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="firstname">
                            <FormItem>
                                <FormLabel>Nombres</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="colocar nombre" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="lastname">
                            <FormItem>
                                <FormLabel>Apellidos</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="colocar apellidos" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="address">
                            <FormItem>
                                <FormLabel>Dirección</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Coloca la dirección" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="phone">
                            <FormItem>
                                <FormLabel>Telefóno</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Coloca el telefóno" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                                <FormField v-slot="{ componentField }" name="birthdate">
                            <FormItem>
                                <FormLabel>Cumpleaños</FormLabel>
                                <FormControl>
                                    <Input type="date" placeholder="Coloca el cumpleaños" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-if="props.client_types" v-slot="{ componentField }" name="client_type_id">
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
                                                <SelectItem v-for="type in props.client_types" :key="type.id" :value="type.id">
                                                    {{ type.name }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <Button type="submit">Crear Cliente</Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
//composable
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useCustomer } from '@/composables/useCustomer';
import { InputClient_type } from '@/interface/Inputs';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { AlertCircle } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed} from 'vue';
import { z } from 'zod';

const { createCustomer } = useCustomer();
const page = usePage<SharedData>();

const props = defineProps<{
    client_types: InputClient_type[];

}>();

const hasErrors = computed(() => {
    return page.props.errors && Object.keys(page.props.errors).length > 0;
});
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clientes',
        href: '/panel/customers',
    },
    {
        title: 'Crear cliente',
        href: '/panel/customers/create',
    },
];

// Form validation

const formShema = toTypedSchema(
    z.object({
        code: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'codigo mayor a 2 letras' })
            .max(11, { message: 'codigo menor a 12 letras' }),
        firstname: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'nombre mayor a 2 letras' })
            .max(50, { message: 'nombre menor a 50 letras' }),
        lastname: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'apellido mayor a 2 letras' })
            .max(40, { message: 'apellido menor a 40 letras' }),
        address: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'dirección mayor a 2 letras' })
            .max(100, { message: 'dirección menor a 100 letras' }),
        phone: z
            .string({ message: 'campo obligatorio' })
            .min(1, { message: 'codigo mayor a 2 letras' })
            .max(9, { message: 'codigo menor a 10 letras' }),
        birthdate: z.string(),
        client_type_id: z.number({ message: 'campo obligatorio' }),
            }),
);

const { handleSubmit } = useForm({
    validationSchema: formShema,
    initialValues: {         
    },
});

const onSubmit = handleSubmit((values) => {
    const customerData= {
        code: values.code,
        firstname: values.firstname,
        lastname: values.lastname,
        address: values.address,
        phone: values.phone,
        birthdate: values.birthdate,
        client_type_id: values.client_type_id,
    };
    
    createCustomer(customerData);
});
</script>
<style scoped></style>
