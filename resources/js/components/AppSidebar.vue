<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Box, Building, Folder, LayoutGrid, Map, PackageCheck, ShieldCheck, Syringe, Truck, UserCog, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Principal',
        href: '/dashboard',
        icon: LayoutGrid,
    },
];
const mainNavItems2: NavItem[] = [
    {
        title: 'Proveedores',
        href: '#',
        icon: PackageCheck,
                items: [
            {
        title: 'Proveedores',
        href: '/panel/suppliers',
            },
        ],
    },

    {
        title: 'Doctores',
        href: '/panel/doctors',
        icon: Syringe,
                items: [
            {
           title: 'Doctores',
           href: '/panel/doctors',
            },
        ],
    },
    {
        title: 'Clientes',
        href: '#',
        icon: Users,
        items: [
            {
            title: 'Clientes',
            href: '/panel/customers',
            },
            {
            title: 'Tipos de cliente',
            href: '/panel/clientTypes',
            },
        ],
    },
];
const mainNavItems3: NavItem[] = [
    {
        title: 'Productos',
        href: '#',
        icon: Box,
        items: [
            {
                title: 'Productos Disponibles',
                href: '/panel/products',
            },
            {
                title: 'Precios de Productos',
                href: '/panel/product_prices',
            },
            {
                title: 'Categorías',
                href: '/panel/categories',
            },
            {
                title: 'Laboratorios',
                href: '/panel/laboratories',
            },
        ],
    },
    {
        title: 'Movimientos',
        href: '#',
        icon: Truck,
        items: [
            {
                title: 'Movimientos',
                href: '/panel/movements',
            },
        ],
    },
    {
        title: 'Inventario',
        href: '#',
        icon: Box,
        items: [
            {
                title: 'Inventario',
                href: '/panel/inventory',
            },
        ],
    },
];
const mainNavItems5: NavItem[] = [
    {
        title: 'Ventas',
        href: '/panel/sales',
        icon: Box,
    },
];
const mainNavItems4: NavItem[] = [
    {
        title: 'Usuarios',
        href: '/panel/users',
        icon: Users,
    },
    {
        title: 'Roles',
        href: '/panel/roles',
        icon: UserCog,
    },
];
const mainNavItems6: NavItem[] = [
    {
        title: 'Zonas',
        href: '/panel/zones',
        icon: Map,
    },
    {
        title: 'Locales',
        href: '/panel/locals',
        icon: Building,
    },
];

const mainNavItems7: NavItem[] = [
    {
        title: 'Permisos',
        href: '/panel/permissions',
        icon: ShieldCheck,
    },
];
const footerNavItems: NavItem[] = [
    {
        title: 'Github',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" :items2="mainNavItems2" :items3="mainNavItems3" :items4="mainNavItems4" :items5="mainNavItems5" :items6="mainNavItems6" :items7="mainNavItems7"/>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
