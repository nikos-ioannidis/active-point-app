<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Employee {
    id: number;
    employee_name: string;
    employee_code: string;
    job_title: string;
    is_active: boolean;
    user: User;
}

interface Props {
    employees: Employee[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Employee Work Types',
        href: '/employee-work-types',
    },
];
</script>

<template>
    <Head title="Employee Work Types" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container py-6">
            <Heading title="Employee Work Types" description="Manage work type assignments for employees" />

            <div class="mt-6 rounded-md border">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Employee Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Job Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="employee in employees" :key="employee.id" class="hover:bg-muted/50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ employee.employee_code }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    {{ employee.employee_name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ employee.job_title }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        :class="{
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium': true,
                                            'bg-green-100 text-green-800': employee.is_active,
                                            'bg-red-100 text-red-800': !employee.is_active,
                                        }"
                                    >
                                        {{ employee.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <Link :href="route('employee-work-types.edit', employee.id)">
                                        <Button variant="outline" size="sm">
                                            Manage Work Types
                                        </Button>
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="employees.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-sm">No employees found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
