<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Pencil } from 'lucide-vue-next';
import moment from 'moment';

interface Employee {
    id: number;
    employee_name: string;
    is_active: boolean;
}

interface Vehicle {
    id: number;
    license_plate: string;
}

interface WorkType {
    id: number;
    name: string;
    work_category_id: number;
}

interface WorkEntry {
    id: number;
    daily_report_id: number;
    work_type_id: number;
    start_time: string;
    end_time: string;
    description: string | null;
    created_at: string;
    updated_at: string;
    work_type: WorkType;
}

interface DailyReport {
    id: number;
    employee_id: number;
    report_date: string;
    job_name: string;
    vehicle_id: number | null;
    notes: string | null;
    total_minutes: number | null;
    created_at: string;
    updated_at: string;
    employee: Employee;
    vehicle: Vehicle | null;
    work_entries: WorkEntry[];
}

interface Props {
    report: DailyReport;
}

const props = defineProps<Props>();

// Format date for display
const formatDate = (dateString: string) => {
    return moment(dateString).format('DD/MM/YYYY');
};

// Format time for display
const formatTime = (timeString: string) => {
    return moment(timeString, 'HH:mm:ss').format('HH:mm');
};

// Format total minutes to hours and minutes
const formatHoursAndMinutes = (totalMinutes: number) => {
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return `${hours}h ${minutes}m`;
};

// Calculate duration between two times
const calculateDuration = (startTime: string, endTime: string) => {
    const start = moment(`${props.report.report_date} ${startTime}`, 'YYYY-MM-DD HH:mm:ss');
    const end = moment(`${props.report.report_date} ${endTime}`, 'YYYY-MM-DD HH:mm:ss');

    // If end time is before start time, assume it's the next day
    if (end.isBefore(start)) {
        end.add(1, 'day');
    }

    const duration = moment.duration(end.diff(start));
    const hours = Math.floor(duration.asHours());
    const minutes = duration.minutes();

    return `${hours}h ${minutes}m`;
};

// Calculate total hours worked
const totalHoursWorked = () => {
    let totalMinutes = 0;

    props.report.work_entries.forEach(entry => {
        const start = moment(`${props.report.report_date} ${entry.start_time}`, 'YYYY-MM-DD HH:mm:ss');
        const end = moment(`${props.report.report_date} ${entry.end_time}`, 'YYYY-MM-DD HH:mm:ss');

        // If end time is before start time, assume it's the next day
        if (end.isBefore(start)) {
            end.add(1, 'day');
        }

        const duration = moment.duration(end.diff(start));
        totalMinutes += duration.asMinutes();
    });

    const hours = Math.floor(totalMinutes / 60);
    const minutes = Math.floor(totalMinutes % 60);

    return `${hours}h ${minutes}m`;
};

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
        title: `Report: ${formatDate(props.report.report_date)}`,
        href: `/daily-reports/${props.report.id}`,
    },
];
</script>

<template>
    <Head :title="`Daily Report - ${formatDate(report.report_date)}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="flex items-start space-x-2">
                <Link :href="route('daily-reports.index')" class="text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-6 w-6" />
                </Link>
                <Heading
                    :title="`Daily Report: ${formatDate(report.report_date)}`"
                    :description="`Report details for ${report.employee.employee_name}${!report.employee.is_active ? ' (Archived)' : ''}`"
                />
            </div>

            <div class="rounded-lg border p-6">
                <div class="mb-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Date</h3>
                        <p class="mt-1 text-lg font-semibold">{{ formatDate(report.report_date) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Technician</h3>
                        <p class="mt-1 text-lg font-semibold">
                            {{ report.employee.employee_name }}
                            <span v-if="!report.employee.is_active" class="ml-2 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Archived</span>
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Job</h3>
                        <p class="mt-1 text-lg font-semibold">{{ report.job_name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Vehicle</h3>
                        <p class="mt-1 text-lg font-semibold">
                            {{ report.vehicle ? report.vehicle.license_plate : 'None' }}
                        </p>
                    </div>
                    <div v-if="report.notes" class="md:col-span-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Notes</h3>
                        <p class="mt-1 whitespace-pre-wrap text-base">{{ report.notes }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="mb-2 text-lg font-medium">Work Entries</h3>
                    <p class="mb-4 text-sm text-muted-foreground">
                        Total hours worked: <span class="font-semibold">{{ report.total_minutes ? formatHoursAndMinutes(report.total_minutes) : totalHoursWorked() }}</span>
                    </p>
                </div>

                <div class="overflow-x-auto rounded-md border">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Work Type
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Start Time
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    End Time
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Duration
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Description
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="entry in report.work_entries" :key="entry.id">
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ entry.work_type.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ formatTime(entry.start_time) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ formatTime(entry.end_time) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ calculateDuration(entry.start_time, entry.end_time) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ entry.description || '-' }}
                                </td>
                            </tr>
                            <tr v-if="report.work_entries.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No work entries found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <Link :href="route('daily-reports.edit', report.id)">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit Report
                        </Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
