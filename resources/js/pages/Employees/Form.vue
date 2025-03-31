<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Pencil } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Employee {
    id?: number;
    user_id?: number | null;
    employee_code: string;
    employee_name: string;
    job_title: string;
    phone_number: string;
    is_active: boolean;
    is_contractor: boolean;
    owns_equipment: boolean;
    irata_level: string;
    created_at?: string;
    updated_at?: string;
    user?: User;
}

interface IrataLevel {
    value: string;
    label: string;
}

interface Props {
    employee?: Employee;
    users?: User[];
    irataLevels?: IrataLevel[];
    mode: 'create' | 'edit' | 'show';
}

const props = defineProps<Props>();

const form = useForm({
    user_id: props.employee?.user_id || null,
    employee_code: props.employee?.employee_code || '',
    employee_name: props.employee?.employee_name || '',
    job_title: props.employee?.job_title || '',
    phone_number: props.employee?.phone_number || '',
    is_active: props.employee?.is_active ?? true,
    is_contractor: props.employee?.is_contractor ?? false,
    owns_equipment: props.employee?.owns_equipment ?? false,
    irata_level: props.employee?.irata_level || 'None',
});

const submit = () => {
    if (props.mode === 'create') {
        form.post(route('employees.store'));
    } else if (props.mode === 'edit' && props.employee?.id) {
        form.put(route('employees.update', props.employee.id));
    }
};

const getPageTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create Employee';
        case 'edit':
            return `Edit Employee: ${props.employee?.employee_name}`;
        case 'show':
            return 'Employee Details';
        default:
            return 'Employee';
    }
};

const getHeadingTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create New Employee';
        case 'edit':
            return `Edit Employee`;
        case 'show':
            return 'Employee Details';
        default:
            return 'Employee';
    }
};

const getHeadingDescription = () => {
    switch (props.mode) {
        case 'create':
            return 'Add a new employee to the system';
        case 'edit':
            return `Update information for ${props.employee?.employee_name}`;
        case 'show':
            return `View information for ${props.employee?.employee_name}`;
        default:
            return '';
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Employees',
        href: '/employees',
    },
    {
        title: getPageTitle(),
        href:
            props.mode === 'create'
                ? '/employees/create'
                : props.employee?.id
                  ? props.mode === 'edit'
                      ? `/employees/${props.employee.id}/edit`
                      : `/employees/${props.employee.id}`
                  : '',
    },
];

const isReadOnly = props.mode === 'show';
</script>

<template>
    <Head :title="getPageTitle()" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="flex items-start space-x-2">
                <Link :href="route('employees.index')" class="text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-6 w-6" />
                </Link>
                <Heading :title="getHeadingTitle()" :description="getHeadingDescription()" />
            </div>

            <!-- Show mode -->
            <div v-if="isReadOnly && employee" class="rounded-lg border p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Employee Code</h3>
                        <p class="mt-1 text-lg font-semibold">{{ employee.employee_code }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Name</h3>
                        <p class="mt-1 text-lg font-semibold">{{ employee.employee_name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Job Title</h3>
                        <p class="mt-1 text-lg font-semibold">{{ employee.job_title }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Phone Number</h3>
                        <p class="mt-1 text-lg font-semibold">{{ employee.phone_number }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">IRATA Level</h3>
                        <p class="mt-1 text-lg font-semibold">
                            {{ irataLevels?.find(level => level.value === employee?.irata_level)?.label || employee.irata_level }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Status</h3>
                        <p class="mt-1 text-lg font-semibold">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="employee.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            >
                                {{ employee.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Is Contractor</h3>
                        <p class="mt-1 text-lg font-semibold">{{ employee.is_contractor ? 'Yes' : 'No' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Owns Equipment?</h3>
                        <p class="mt-1 text-lg font-semibold">{{ employee.owns_equipment ? 'Yes' : 'No' }}</p>
                    </div>
                    <div v-if="employee.user">
                        <h3 class="text-sm font-medium text-muted-foreground">Associated User</h3>
                        <p class="mt-1 text-lg font-semibold">{{ employee.user.name }} ({{ employee.user.email }})</p>
                    </div>
                    <!-- <div v-if="employee.created_at">
                        <h3 class="text-sm font-medium text-muted-foreground">Created At</h3>
                        <p class="mt-1 text-lg font-semibold">{{ formatDate(employee.created_at) }}</p>
                    </div> -->
                </div>
                <div class="mt-6 flex justify-end">
                    <Link :href="route('employees.edit', employee.id)">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit Employee
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Create/Edit mode -->
            <form v-else @submit.prevent="submit" class="space-y-6 rounded-lg border p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="employee_code">Employee Code</Label>
                        <Input id="employee_code" v-model="form.employee_code" type="text" required placeholder="Employee code" />
                        <InputError :message="form.errors.employee_code" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="employee_name">Employee Name</Label>
                        <Input id="employee_name" v-model="form.employee_name" type="text" required placeholder="Full name" />
                        <InputError :message="form.errors.employee_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="job_title">Job Title</Label>
                        <Input id="job_title" v-model="form.job_title" type="text" required placeholder="Job title" />
                        <InputError :message="form.errors.job_title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="phone_number">Phone Number</Label>
                        <Input id="phone_number" v-model="form.phone_number" type="text" required placeholder="Phone number" />
                        <InputError :message="form.errors.phone_number" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="irata_level">IRATA Level</Label>
                        <select
                            id="irata_level"
                            v-model="form.irata_level"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            required
                        >
                            <option v-for="level in irataLevels" :key="level.value" :value="level.value">
                                {{ level.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.irata_level" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="user_id">Associated User (Optional)</Label>
                        <select
                            id="user_id"
                            v-model="form.user_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option :value="null">None</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                        <InputError :message="form.errors.user_id" />
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <input
                            id="is_active"
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                        />
                        <Label for="is_active">Active Employee</Label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <input
                            id="is_contractor"
                            v-model="form.is_contractor"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                        />
                        <Label for="is_contractor">Is Contractor</Label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <input
                            id="owns_equipment"
                            v-model="form.owns_equipment"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                        />
                        <Label for="owns_equipment">Owns Equipment</Label>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ props.mode === 'create' ? 'Create Employee' : 'Update Employee' }}
                    </Button>
                    <Link :href="route('employees.index')">
                        <Button variant="outline" type="button">Cancel</Button>
                    </Link>
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                    </Transition>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
