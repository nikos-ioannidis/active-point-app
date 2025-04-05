<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { PlusCircle, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import moment from 'moment';
import { ref } from 'vue';

interface Employee {
    id: number;
    employee_name: string;
    is_active: boolean;
}

interface Vehicle {
    id: number;
    license_plate: string;
}

interface WorkJob {
    id: number;
    code: string;
    description: string;
    client_name: string;
    client_id: string;
}

interface DailyReport {
    id: number;
    employee_id: number;
    report_date: string;
    work_job_id: number | null;
    vehicle_id: number | null;
    notes: string | null;
    total_minutes: number | null;
    created_at: string;
    updated_at: string;
    employee?: Employee;
    vehicle?: Vehicle;
    workJob?: WorkJob;
}

interface Props {
    reports: {
        data: DailyReport[];
        links: any[];
        meta: {
            current_page: number;
            from: number;
            last_page: number;
            links: any[];
            path: string;
            per_page: number;
            to: number;
            total: number;
        };
    };
}

defineProps<Props>();

// Format date for display
const formatDate = (dateString: string) => {
    return moment(dateString).format('DD/MM/YYYY');
};

// Format total minutes to hours and minutes
const formatHoursAndMinutes = (totalMinutes: number) => {
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return `${hours}h ${minutes}m`;
};

// Confirm delete
const reportToDelete = ref<DailyReport | null>(null);
const showDeleteConfirm = ref(false);

const confirmDelete = (report: DailyReport) => {
    reportToDelete.value = report;
    showDeleteConfirm.value = true;
};

const cancelDelete = () => {
    reportToDelete.value = null;
    showDeleteConfirm.value = false;
};

const deleteReport = () => {
    if (reportToDelete.value) {
        window.location.href = route('daily-reports.destroy', reportToDelete.value.id);
    }
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
];
</script>

<template>
    <Head title="Daily Reports (02)" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="flex items-center justify-between">
                <Heading title="Daily Reports (02)" description="Manage your daily work reports" />
                <Link :href="route('daily-reports.create')">
                    <Button>
                        <PlusCircle class="mr-2 h-4 w-4" />
                        New Report
                    </Button>
                </Link>
            </div>

            <!-- Reports Table -->
            <div class="rounded-lg border">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Technician
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Job
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Vehicle
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Hours
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="report in reports.data" :key="report.id" class="hover:bg-muted/50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ formatDate(report.report_date) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span>
                                        {{ report.employee?.employee_name || 'N/A' }}
                                        <span v-if="report.employee && !report.employee.is_active" class="ml-1 rounded-full bg-red-100 px-1.5 py-0.5 text-xs font-medium text-red-800">Archived</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div v-if="report.workJob">
                                        <div class="font-medium">{{ report.workJob.code }} - {{ report.workJob.description }}</div>
                                        <div class="text-sm text-gray-500">{{ report.workJob.client_name }}</div>
                                    </div>
                                    <div v-else>
                                        N/A
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ report.vehicle?.license_plate || 'None' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ report.total_minutes ? formatHoursAndMinutes(report.total_minutes) : '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <Link :href="route('daily-reports.show', report.id)">
                                            <Button variant="outline" size="icon">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Link :href="route('daily-reports.edit', report.id)">
                                            <Button variant="outline" size="icon">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button variant="destructive" size="icon" @click="confirmDelete(report)">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="reports.data.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No reports found. Create your first report!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="reports.meta && reports.meta.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    <Link
                        v-if="reports.meta && reports.meta.current_page > 1 && reports.links && reports.links.length > 0"
                        :href="reports.links[0].url"
                        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Previous
                    </Link>
                    <Link
                        v-if="reports.meta && reports.meta.current_page < reports.meta.last_page && reports.links && reports.links.length > 0"
                        :href="reports.links[reports.links.length - 1].url"
                        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Next
                    </Link>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ reports.meta ? reports.meta.from : 0 }}</span>
                            to
                            <span class="font-medium">{{ reports.meta ? reports.meta.to : 0 }}</span>
                            of
                            <span class="font-medium">{{ reports.meta ? reports.meta.total : 0 }}</span>
                            results
                        </p>
                    </div>
                    <div v-if="reports.meta && reports.meta.links">
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <Link
                                v-for="(link, i) in reports.meta.links"
                                :key="i"
                                :href="link.url"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 text-sm font-medium',
                                    link.active
                                        ? 'z-10 bg-indigo-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600'
                                        : 'text-gray-500 hover:bg-gray-50 focus:z-20',
                                    i === 0 ? 'rounded-l-md' : '',
                                    i === reports.meta.links.length - 1 ? 'rounded-r-md' : '',
                                ]"
                            >
                                <span v-if="link.label && link.label.includes('Previous')">&laquo; Previous</span>
                                <span v-else-if="link.label && link.label.includes('Next')">Next &raquo;</span>
                                <span v-else>{{ link.label }}</span>
                            </Link>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                    <h3 class="mb-4 text-lg font-medium">Confirm Delete</h3>
                    <p class="mb-6 text-sm text-gray-600">
                        Are you sure you want to delete the report for {{ formatDate(reportToDelete?.report_date || '') }}? This action
                        cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-2">
                        <Button variant="outline" @click="cancelDelete">Cancel</Button>
                        <Button variant="destructive" @click="deleteReport">Delete</Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
