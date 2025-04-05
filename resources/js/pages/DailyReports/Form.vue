<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Trash } from 'lucide-vue-next';
import moment from 'moment';
import { computed } from 'vue';

interface Employee {
    id: number;
    employee_name: string;
    employee_code: string;
    is_active: boolean;
}

interface Vehicle {
    id: number;
    license_plate: string;
}

interface WorkType {
    id: number;
    name: string;
}

interface WorkCategory {
    id: number;
    name: string;
    types: WorkType[];
}

interface WorkJob {
    id: number;
    code: string;
    description: string;
    client_name: string;
    client_id: string;
}

interface WorkEntry {
    id?: number;
    work_type_id: number | null;
    start_time: string;
    end_time: string;
    description: string | null;
}

interface DailyReport {
    id?: number;
    employee_id: number;
    report_date: string;
    work_job_id: number | null;
    vehicle_id: number | null;
    notes: string | null;
    work_entries: WorkEntry[];
}

interface Props {
    employee: Employee;
    vehicles: Vehicle[];
    workTypes: WorkCategory[];
    workJobs: WorkJob[];
    report?: DailyReport;
    mode: 'create' | 'edit';
    isAdmin: boolean;
    employees?: Employee[];
}

const props = defineProps<Props>();

// Initialize form with default values or existing report data
const form = useForm({
    employee_id: props.report?.employee_id || props.employee.id,
    report_date: props.report?.report_date || moment().format('YYYY-MM-DD'),
    work_job_id: props.report?.work_job_id || null,
    vehicle_id: props.report?.vehicle_id || null,
    notes: props.report?.notes || '',
    work_entries: props.report?.work_entries || [
        {
            work_type_id: null,
            start_time: '',
            end_time: '',
            description: '',
        },
    ],
});

// Add a new work entry row
const addWorkEntry = () => {
    form.work_entries.push({
        work_type_id: null,
        start_time: '',
        end_time: '',
        description: '',
    });
};

// Remove a work entry row
const removeWorkEntry = (index: number) => {
    if (form.work_entries.length > 1) {
        form.work_entries.splice(index, 1);
    }
};

// Submit the form
const submit = () => {
    if (props.mode === 'create') {
        form.post(route('daily-reports.store'));
    } else if (props.mode === 'edit' && props.report?.id) {
        form.put(route('daily-reports.update', props.report.id));
    }
};

// Get page title based on mode
const getPageTitle = computed(() => {
    return props.mode === 'create' ? 'Create Daily Report' : 'Edit Daily Report';
});

// Get heading title based on mode
const getHeadingTitle = computed(() => {
    return props.mode === 'create' ? 'Create New Daily Report' : 'Edit Daily Report';
});

// Get heading description based on mode
const getHeadingDescription = computed(() => {
    return props.mode === 'create' ? 'Fill out the form to create a new daily report' : 'Update the daily report information';
});

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Daily Reports (02)',
        href: '/daily-reports',
    },
    {
        title: getPageTitle.value,
        href: props.mode === 'create' ? '/daily-reports/create' : `/daily-reports/${props.report?.id}/edit`,
    },
];
</script>

<template>
    <Head :title="getPageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="flex items-start space-x-2">
                <Link :href="route('daily-reports.index')" class="text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-6 w-6" />
                </Link>
                <Heading :title="getHeadingTitle" :description="getHeadingDescription" />
            </div>

            <form @submit.prevent="submit" class="space-y-6 rounded-lg border p-6">
                <!-- Report Information -->
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="report_date">Report Date</Label>
                        <Input id="report_date" v-model="form.report_date" type="date" required :max="moment().format('YYYY-MM-DD')" />
                        <InputError :message="form.errors.report_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="employee">Technician</Label>
                        <template v-if="mode === 'create' && isAdmin && employees">
                            <select
                                id="employee_id"
                                v-model="form.employee_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            >
                                <option :value="null" disabled>Select technician</option>
                                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                                    {{ emp.employee_name }} ({{ emp.employee_code }}){{ !emp.is_active ? ' - Archived' : '' }}
                                </option>
                            </select>
                            <InputError :message="form.errors.employee_id" />
                        </template>
                        <template v-else-if="mode === 'edit' && isAdmin && employees">
                            <div class="fake-input">
                                {{ employees?.filter((emp) => emp.id === form.employee_id)[0].employee_name }}
                                {{ !employees?.filter((emp) => emp.id === form.employee_id)[0].is_active ? ' - Archived' : '' }}
                            </div>
                        </template>
                        <template v-else>
                            <Input id="employee" type="text" :value="employee.employee_name" disabled class="bg-gray-100" />
                        </template>
                    </div>

                    <div class="grid gap-2">
                        <Label for="work_job_id">Job</Label>
                        <select
                            id="work_job_id"
                            v-model="form.work_job_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            required
                        >
                            <option :value="null" disabled>Select job</option>
                            <option v-for="job in workJobs" :key="job.id" :value="job.id">
                                {{ job.code }} - {{ job.description }} ({{ job.client_name }})
                            </option>
                        </select>
                        <InputError :message="form.errors.work_job_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="vehicle_id">Vehicle Used (Optional)</Label>
                        <select
                            id="vehicle_id"
                            v-model="form.vehicle_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option :value="null">None</option>
                            <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">
                                {{ vehicle.license_plate }}
                            </option>
                        </select>
                        <InputError :message="form.errors.vehicle_id" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="notes">Notes (Optional)</Label>
                    <Textarea id="notes" v-model="form.notes" placeholder="Any additional notes about the report" :rows="3" />
                    <InputError :message="form.errors.notes" />
                </div>

                <!-- Work Entries Table -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">Work Entries</h3>
                        <Button type="button" variant="outline" @click="addWorkEntry">
                            <Plus class="mr-2 h-4 w-4" />
                            Add Entry
                        </Button>
                    </div>

                    <div class="rounded-lg border">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-muted/50">
                                    <tr class="border-b">
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Work Type
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Start Time
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            End Time
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Description
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <tr v-for="(entry, index) in form.work_entries" :key="index" class="hover:bg-muted/50">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <select
                                                v-model="entry.work_type_id"
                                                class="w-full rounded-md border border-input bg-background px-3 py-1 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                                required
                                            >
                                                <option :value="null" disabled>Select work type</option>
                                                <option v-for="category in workTypes" :key="category.id" :value="category.id">
                                                    {{ category.name }}
                                                </option>
                                            </select>
                                            <InputError :message="form.errors[`work_entries.${index}.work_type_id`]" />
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <Input v-model="entry.start_time" type="time" required class="w-full" />
                                            <InputError :message="form.errors[`work_entries.${index}.start_time`]" />
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <Input v-model="entry.end_time" type="time" required class="w-full" />
                                            <InputError :message="form.errors[`work_entries.${index}.end_time`]" />
                                        </td>
                                        <td class="px-6 py-4">
                                            <Input v-model="entry.description" type="text" placeholder="Optional description" class="w-full" />
                                            <InputError :message="form.errors[`work_entries.${index}.description`]" />
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                @click="removeWorkEntry(index)"
                                                :disabled="form.work_entries.length <= 1"
                                            >
                                                <Trash class="h-4 w-4 text-red-500" />
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <InputError :message="form.errors.work_entries" />
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ mode === 'create' ? 'Create Report' : 'Update Report' }}
                    </Button>
                    <Link :href="route('daily-reports.index')">
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

<style scoped lang="scss">
.fake-input {
    background-color: #f3f4f6;
    padding: 0.5rem;
    border-radius: 0.375rem;
    color: #374151;
    font-size: 0.875rem;
}

html.dark {
    .fake-input {
        background-color: #0a0a0a;
        color: inherit;
    }
}
</style>
