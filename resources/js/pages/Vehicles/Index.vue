<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/ui/pagination';
import AppLayout from '@/layouts/AppLayout.vue';
import { debounce } from '@/lib/utils';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Eye, Pencil, PlusCircle, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const page = usePage<SharedData>();

interface Vehicle {
    id: number;
    license_plate: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    vehicles: {
        data: Vehicle[];
        links: any[];
        meta: any;
    };
    filters: {
        search: string;
        status: string | null;
    };
}

const props = defineProps<Props>();
const search = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');

// Debounce search to avoid too many requests
const debouncedSearch = debounce(() => {
    router.get(
        route('vehicles.index'),
        { search: search.value, status: selectedStatus.value },
        { preserveState: true, replace: true }
    );
}, 600);

watch(search, () => {
    debouncedSearch();
});

watch(selectedStatus, () => {
    router.get(
        route('vehicles.index'),
        { search: search.value, status: selectedStatus.value },
        { preserveState: true, replace: true }
    );
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Vehicles',
        href: '/vehicles',
    },
];

</script>

<template>
    <Head title="Vehicles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="flex items-center justify-between">
                <Heading title="Vehicles" description="Manage vehicles in the system" />
                <Link :href="route('vehicles.create')" v-if="(page.props.auth.user as any).roles?.includes('Admin')">
                    <Button>
                        <PlusCircle class="mr-2 h-4 w-4" />
                        Add Vehicle
                    </Button>
                </Link>
            </div>

            <!-- Search and Filter -->
            <div class="flex items-center space-x-4">
                <div class="relative flex-1">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input type="search" placeholder="Search license plates..." class="pl-8" v-model="search" />
                </div>
                <div class="w-64">
                    <select
                        v-model="selectedStatus"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="rounded-lg border">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">License Plate</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="vehicle in vehicles.data" :key="vehicle.id" class="hover:bg-muted/50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    {{ vehicle.id }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    {{ vehicle.license_plate }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        :class="{
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium': true,
                                            'bg-green-100 text-green-800': vehicle.is_active,
                                            'bg-red-100 text-red-800': !vehicle.is_active,
                                        }"
                                    >
                                        {{ vehicle.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <div class="flex space-x-2">
                                        <Link :href="route('vehicles.show', vehicle.id)">
                                            <Button variant="outline" size="icon">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Link :href="route('vehicles.edit', vehicle.id)" v-if="(page.props.auth.user as any).roles?.includes('Admin')">
                                            <Button variant="outline" size="icon">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="vehicles.data.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-sm">No vehicles found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination :links="vehicles.links" />
        </div>
    </AppLayout>
</template>
