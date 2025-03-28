<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/ui/pagination';
import AppLayout from '@/layouts/AppLayout.vue';
import { debounce } from '@/lib/utils';
import { formatPrice } from '@/lib/helpers';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Pencil, PlusCircle, Search, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface WorkCategory {
    id: number;
    name: string;
}

interface WorkType {
    id: number;
    work_category_id: number;
    name: string;
    price_standard: number;
    price_gamesa: number;
    price_gamesa_abroad: number;
    max_hours: number;
    created_at: string;
    updated_at: string;
    work_category: WorkCategory;
}

interface Props {
    workTypes: {
        data: WorkType[];
        links: any[];
        meta: any;
    };
    categories: WorkCategory[];
    filters: {
        search: string;
        category: number | null;
    };
}

const props = defineProps<Props>();
const workTypeToDelete = ref<WorkType | null>(null);
const isDeleteDialogOpen = ref(false);
const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');

const confirmDelete = (workType: WorkType) => {
    workTypeToDelete.value = workType;
    isDeleteDialogOpen.value = true;
};

// Debounce search to avoid too many requests
const debouncedSearch = debounce(() => {
    router.get(
        route('work-types.index'),
        { search: search.value, category: selectedCategory.value },
        { preserveState: true, replace: true }
    );
}, 600);

watch(search, () => {
    debouncedSearch();
});

watch(selectedCategory, () => {
    router.get(
        route('work-types.index'),
        { search: search.value, category: selectedCategory.value },
        { preserveState: true, replace: true }
    );
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Work Types',
        href: '/work-types',
    },
];

</script>

<template>
    <Head title="Work Types" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="flex items-center justify-between">
                <Heading title="Work Types" description="Manage work types in the system" />
                <Link :href="route('work-types.create')" v-if="$page.props.auth.user.roles.includes('Admin')">
                    <Button>
                        <PlusCircle class="mr-2 h-4 w-4" />
                        Add Work Type
                    </Button>
                </Link>
            </div>

            <!-- Search and Filter -->
            <div class="flex items-center space-x-4">
                <div class="relative flex-1">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input type="search" placeholder="Search work types..." class="pl-8" v-model="search" />
                </div>
                <div class="w-64">
                    <select
                        v-model="selectedCategory"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="">All Categories</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="rounded-lg border">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Category</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Standard Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Gamesa Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Gamesa Abroad</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Max Hours</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="workType in workTypes.data" :key="workType.id" class="hover:bg-muted/50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    {{ workType.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ workType.work_category.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ formatPrice(workType.price_standard) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ formatPrice(workType.price_gamesa) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ formatPrice(workType.price_gamesa_abroad) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ workType.max_hours }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <div class="flex space-x-2">
                                        <Link :href="route('work-types.show', workType.id)">
                                            <Button variant="outline" size="icon">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Link :href="route('work-types.edit', workType.id)" v-if="$page.props.auth.user.roles.includes('Admin')">
                                            <Button variant="outline" size="icon">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="destructive"
                                            size="icon"
                                            @click="confirmDelete(workType)"
                                            v-if="$page.props.auth.user.roles.includes('Admin')"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="workTypes.data.length === 0">
                                <td colspan="7" class="px-6 py-4 text-center text-sm">No work types found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination :links="workTypes.links" />
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Work Type</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete {{ workTypeToDelete?.name }}? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="isDeleteDialogOpen = false">Cancel</Button>
                    <Button
                        variant="destructive"
                        @click="
                            () => {
                                router.delete(route('work-types.destroy', workTypeToDelete?.id), {
                                    onFinish: () => (isDeleteDialogOpen = false),
                                });
                            }
                        "
                    >
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
