<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/ui/pagination';
import AppLayout from '@/layouts/AppLayout.vue';
import { debounce } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { type PageProps } from '@inertiajs/core';
import { Eye, Pencil, PlusCircle, Search, Archive } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

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

interface User {
    id: number;
    name: string;
    email: string;
}

interface Employee {
    id: number;
    user_id: number | null;
    employee_code: string;
    employee_name: string;
    job_title: string;
    phone_number: string;
    is_active: boolean;
    is_contractor: boolean;
    owns_equipment: boolean;
    irata_level: string;
    irata_level_label?: string;
    created_at: string;
    updated_at: string;
    user?: User;
}

interface Props {
    employees: {
        data: Employee[];
        links: any[];
        meta: any;
    };
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();
const employeeToArchive = ref<Employee | null>(null);
const isArchiveDialogOpen = ref(false);
const search = ref(props.filters.search || '');

const confirmArchive = (employee: Employee) => {
    employeeToArchive.value = employee;
    isArchiveDialogOpen.value = true;
};

// Debounce search to avoid too many requests
const debouncedSearch = debounce(() => {
    router.get(route('employees.index'), { search: search.value }, { preserveState: true, replace: true });
}, 600);

watch(search, () => {
    debouncedSearch();
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Employees',
        href: '/employees',
    },
];

const getStatusClass = (isActive: boolean) => {
    return isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
};

const getIsContractorClass = (isContractor: boolean) => {
    return isContractor ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Employees" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="flex items-center justify-between">
                <Heading title="Employees" description="Manage employees in the system" />
                <Link :href="route('employees.create')" v-if="userRoles.includes('Admin')">
                    <Button>
                        <PlusCircle class="mr-2 h-4 w-4" />
                        Add Employee
                    </Button>
                </Link>
            </div>

            <!-- Search -->
            <div class="flex items-center space-x-2">
                <div class="relative flex-1">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input type="search" placeholder="Search employees..." class="pl-8" v-model="search" />
                </div>
            </div>

            <div class="rounded-lg border">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Job Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">IRATA Level</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Is Contractor</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="employee in employees.data" :key="employee.id" class="hover:bg-muted/50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    {{ employee.employee_code }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ employee.employee_name }}<br />
                                    <small v-if="employee?.user">{{ employee?.user?.name }} ({{ employee?.user?.email }})</small>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ employee.job_title }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ employee.irata_level_label || employee.irata_level }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="getStatusClass(employee.is_active)"
                                    >
                                        {{ employee.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="getIsContractorClass(employee.is_contractor)"
                                    >
                                        {{ employee.is_contractor ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <div class="flex space-x-2">
                                        <Link :href="route('employees.show', employee.id)">
                                            <Button variant="outline" size="icon">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Link :href="route('employees.edit', employee.id)" v-if="userRoles.includes('Admin')">
                                            <Button variant="outline" size="icon">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="destructive"
                                            size="icon"
                                            @click="confirmArchive(employee)"
                                            v-if="userRoles.includes('Admin') && employee.is_active"
                                        >
                                            <Archive class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="employees.data.length === 0">
                                <td colspan="7" class="px-6 py-4 text-center text-sm">No employees found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination :links="employees.links" />
        </div>

        <!-- Archive Confirmation Dialog -->
        <Dialog v-model:open="isArchiveDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Archive Employee</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to archive {{ employeeToArchive?.employee_name }}? The employee will be marked as inactive but their data will still be accessible in reports.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="isArchiveDialogOpen = false">Cancel</Button>
                    <Button
                        variant="destructive"
                        @click="
                            () => {
                                router.delete(route('employees.destroy', employeeToArchive?.id), {
                                    onFinish: () => (isArchiveDialogOpen = false),
                                });
                            }
                        "
                    >
                        Archive
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
