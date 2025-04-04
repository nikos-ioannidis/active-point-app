<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/ui/pagination';
import AppLayout from '@/layouts/AppLayout.vue';
import { debounce } from '@/lib/utils';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { Eye, Pencil, PlusCircle, Search, Upload } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const page = usePage<SharedData>();

interface WorkJob {
    id: number;
    code: string;
    description: string;
    client_name: string;
    client_id: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    workJobs: {
        data: WorkJob[];
        links: any[];
        meta: any;
    };
    filters: {
        search: string;
        status: string | null;
        client: string | null;
    };
}

const props = defineProps<Props>();
const search = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');
const clientSearch = ref(props.filters.client || '');

// Debounce search to avoid too many requests
const debouncedSearch = debounce(() => {
    router.get(
        route('work-jobs.index'),
        {
            search: search.value,
            status: selectedStatus.value,
            client: clientSearch.value
        },
        { preserveState: true, replace: true }
    );
}, 600);

watch(search, () => {
    debouncedSearch();
});

watch(selectedStatus, () => {
    router.get(
        route('work-jobs.index'),
        {
            search: search.value,
            status: selectedStatus.value,
            client: clientSearch.value
        },
        { preserveState: true, replace: true }
    );
});

watch(clientSearch, () => {
    debouncedSearch();
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Jobs',
        href: '/work-jobs',
    },
];

// File upload form
const form = useForm({
    file: null as File | null,
});

const fileInputRef = ref<HTMLInputElement | null>(null);

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.file = target.files[0];
    }
};

const submitFile = () => {
    form.post(route('work-jobs.import'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            if (fileInputRef.value) {
                fileInputRef.value.value = '';
            }
        },
    });
};

</script>

<template>
    <Head title="Jobs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="flex items-center justify-between">
                <Heading title="Jobs" description="Manage jobs in the system" />
                <div class="flex space-x-2" v-if="(page.props.auth.user as any).roles?.includes('Admin')">
                    <!-- Import Excel Button -->
                    <form @submit.prevent="submitFile" class="flex items-center space-x-2">
                        <input
                            type="file"
                            ref="fileInputRef"
                            @change="handleFileChange"
                            accept=".xlsx,.xls,.csv"
                            class="hidden"
                            id="file-upload"
                        />
                        <label
                            for="file-upload"
                            class="flex h-10 cursor-pointer items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                        >
                            <Upload class="mr-2 h-4 w-4" />
                            Import Excel
                        </label>
                        <Button type="submit" :disabled="!form.file || form.processing">
                            Upload
                        </Button>
                    </form>

                    <!-- Add Job Button -->
                    <Link :href="route('work-jobs.create')">
                        <Button>
                            <PlusCircle class="mr-2 h-4 w-4" />
                            Add Job
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="flex flex-col space-y-4 md:flex-row md:items-center md:space-x-4 md:space-y-0">
                <div class="relative flex-1">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input type="search" placeholder="Search job codes or descriptions..." class="pl-8" v-model="search" />
                </div>
                <div class="w-full md:w-64">
                    <select
                        v-model="selectedStatus"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="relative flex-1">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input type="search" placeholder="Search clients..." class="pl-8" v-model="clientSearch" />
                </div>
            </div>

            <div class="rounded-lg border">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Job Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Client</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="workJob in workJobs.data" :key="workJob.id" class="hover:bg-muted/50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    {{ workJob.id }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    {{ workJob.code }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="max-w-xs truncate">{{ workJob.description }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <div>{{ workJob.client_name }}</div>
                                    <div class="text-xs text-muted-foreground">ID: {{ workJob.client_id }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        :class="{
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium': true,
                                            'bg-green-100 text-green-800': workJob.is_active,
                                            'bg-red-100 text-red-800': !workJob.is_active,
                                        }"
                                    >
                                        {{ workJob.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <div class="flex space-x-2">
                                        <Link :href="route('work-jobs.show', workJob.id)">
                                            <Button variant="outline" size="icon">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Link :href="route('work-jobs.edit', workJob.id)" v-if="(page.props.auth.user as any).roles?.includes('Admin')">
                                            <Button variant="outline" size="icon">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="workJobs.data.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-sm">No jobs found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination :links="workJobs.links" />
        </div>
    </AppLayout>
</template>
