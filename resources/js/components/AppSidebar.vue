<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Users, Wrench, FolderKanban, Settings, Car, FileText, Briefcase } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

import { type PageProps } from '@inertiajs/core';

interface CustomPageProps extends PageProps {
    auth: {
        user: {
            roles: string[];
            [key: string]: any;
        } | null;
    };
}

const page = usePage<CustomPageProps>();
const user = computed(() => page.props.auth.user);
const userRoles = computed(() => user.value?.roles || []);

// Common items for all users
const dashboardItem: NavItem = {
    title: 'Dashboard',
    href: '/dashboard',
    icon: LayoutGrid,
};

// Admin-specific items
const adminItems: NavItem[] = [
    {
        title: 'Users',
        href: '/users',
        icon: Users,
    },
    {
        title: 'Employees',
        href: '/employees',
        icon: Wrench,
    },
    {
        title: 'Pricing',
        href: '#',
        icon: FolderKanban,
        children: [
            {
                title: 'Categories',
                href: '/work-categories',
            },
            {
                title: 'Types',
                href: '/work-types',
            },
            {
                title: 'Trainings',
                href: '/trainings',
            },
        ],
    },
];

// Technician-specific items
const technicianItems: NavItem[] = [

];

// Shared items
const sharedItems: NavItem[] = [
    {
        title: 'Daily Reports (02)',
        href: '/daily-reports',
        icon: FileText,
    },
    {
        title: 'Vehicles',
        href: '/vehicles',
        icon: Car,
    },
    {
        title: 'Jobs',
        href: '/work-jobs',
        icon: Briefcase,
    },
    {
        title: 'System Settings',
        href: '/system-settings',
        icon: Settings,
    }
];

// Compute the main navigation items based on user roles
const mainNavItems = computed(() => {
    const items = [dashboardItem];

    if (userRoles.value.includes('Admin')) {
        items.push(...adminItems);
    }

    if (userRoles.value.includes('Technician')) {
        items.push(...technicianItems);
    }

    items.push(...sharedItems);

    return items;
});

const footerNavItems: NavItem[] = [];
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
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
