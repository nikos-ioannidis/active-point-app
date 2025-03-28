<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/ui/pagination';
import AppLayout from '@/layouts/AppLayout.vue';
import { debounce } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Pencil, PlusCircle, Search, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface WorkCategory {
    id: number;
    name: string;
    created_at: string;
    updated_at: string;
    work_types_count: number;
}

interface Props {
    workCategories: {
        data: WorkCategory[];
        links: any[];
        meta: any;
    };
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();
const categoryToDelete = ref<WorkCategory | null>(null);
const isDeleteDialogOpen = ref(false);
const search = ref(props.filters.search || '');

const confirmDelete = (category: WorkCategory) => {
    categoryToDelete.value = category;
    isDeleteDialogOpen.value = true;
};

// Debounce search to avoid too many requests
const debouncedSearch = debounce(() => {
    router.get(route('work-categories.index'), { search: search.value }, { preserveState: true, replace: true });
}, 600);

watch(search, () => {
    debouncedSearch();
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Work Categories',
        href: '/work-categories',
    },
];
</script>

<template>
    <Head title="Work Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="flex items-center justify-between">
                <Heading title="Work Categories" description="Manage work categories in the system" />
                <Link :href="route('work-categories.create')" v-if="$page.props.auth.user.roles.includes('Admin')">
                    <Button>
                        <PlusCircle class="mr-2 h-4 w-4" />
                        Add Category
                    </Button>
                </Link>
            </div>

            <!-- Search -->
            <div class="flex items-center space-x-2">
                <div class="relative flex-1">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input type="search" placeholder="Search categories..." class="pl-8" v-model="search" />
                </div>
            </div>

            <div class="rounded-lg border">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Work Types</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="category in workCategories.data" :key="category.id" class="hover:bg-muted/50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    {{ category.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ category.work_types_count }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <div class="flex space-x-2">
                                        <Link :href="route('work-categories.show', category.id)">
                                            <Button variant="outline" size="icon">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Link :href="route('work-categories.edit', category.id)" v-if="$page.props.auth.user.roles.includes('Admin')">
                                            <Button variant="outline" size="icon">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="destructive"
                                            size="icon"
                                            @click="confirmDelete(category)"
                                            v-if="$page.props.auth.user.roles.includes('Admin')"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="workCategories.data.length === 0">
                                <td colspan="3" class="px-6 py-4 text-center text-sm">No work categories found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination :links="workCategories.links" />
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Work Category</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete {{ categoryToDelete?.name }}? This action cannot be undone and will also delete all associated work types.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="isDeleteDialogOpen = false">Cancel</Button>
                    <Button
                        variant="destructive"
                        @click="
                            () => {
                                router.delete(route('work-categories.destroy', categoryToDelete?.id), {
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
