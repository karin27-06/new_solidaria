<template>
    <Head title="Nuevo Doctor"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="mt-4 flex flex-col gap-4">
                <CardHeader>
                    <CardTitle>NUEVO DOCTOR</CardTitle>
                    <CardDescription>Complete los campos para registrar un nuevo doctor</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit="onSubmit" class="flex flex-col gap-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Nombre</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Nombre" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="code">
                            <FormItem>
                                <FormLabel>Código</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Código" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField v-slot="{ componentField }" name="start_date">
                            <FormItem>
                                <FormLabel>Fecha de inicio</FormLabel>
                                <FormControl>
                                    <Input type="date" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <div class="container flex justify-end gap-4">
                            <Button type="submit" variant="default"> Enviar </Button>
                            <Button type="reset" variant="outline"> Borrar </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';

import { useDoctors } from '@/composables/useDoctor';
import * as z from 'zod';
const { createDoctor } = useDoctors();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Doctores',
        href: '/panel/doctors',
    },
    {
        title: 'Registrar doctor',
        href: '/panel/doctors/create',
    },
];

// Form validation schema
const formSchema = toTypedSchema(
    z.object({
        name: z
            .string({ message: 'Campo obligatorio' })
            .min(2, { message: 'El nombre debe tener al menos 2 caracteres' })
            .max(150, { message: 'El nombre debe tener menos de 150 caracteres' }),
        code: z
            .string({ message: 'Campo obligatorio' })
            .min(2, { message: 'El código debe tener al menos 2 caracteres' })
            .max(6, { message: 'El código debe tener menos de 6 caracteres' }),
        start_date: z.string().refine((date) => !isNaN(Date.parse(date)), { message: 'Fecha inválida' }),
    }),
);

// Form submission
const { handleSubmit } = useForm({
    validationSchema: formSchema,
});
const onSubmit = handleSubmit((values) => {
    console.log('Formulario enviado con:', values);
    createDoctor(values);
    // Emit or handle the form submission logic here
});
</script>

<style scoped></style>
