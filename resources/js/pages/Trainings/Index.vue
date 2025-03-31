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
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { type PageProps } from '@inertiajs/core';
import { Eye, Pencil, PlusCircle, Search, Trash2 } from 'lucide-vue-next';
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

interface Training {
    id: number;
    name: string;
    price_standard: number;
    max_hours: number;
    is_irata: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    trainings: {
        data: Training[];
        links: any[];
        meta: any;
    };
    filters: {
        search: string;
    };
}

const props = defineProps<Props>();
const trainingToDelete = ref<Training | null>(null);
const isDeleteDialogOpen = ref(false);
const search = ref(props.filters.search || '');

const confirmDelete = (training: Training) => {
    trainingToDelete.value = training;
    isDeleteDialogOpen.value = true;
};

// Debounce search to avoid too many requests
const debouncedSearch = debounce(() => {
    router.get(
        route('trainings.index'),
        { search: search.value },
        { preserveState: true, replace: true }
    );
}, 600);

watch(search, () => {
    debouncedSearch();
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Trainings',
        href: '/trainings',
    },
];

</script>

<template>
    <Head title="Trainings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="flex items-center justify-between">
                <Heading title="Trainings" description="Manage trainings in the system" />
                <Link :href="route('trainings.create')" v-if="userRoles.includes('Admin')">
                    <Button>
                        <PlusCircle class="mr-2 h-4 w-4" />
                        Add Training
                    </Button>
                </Link>
            </div>

            <!-- Search -->
            <div class="flex items-center space-x-4">
                <div class="relative flex-1">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input type="search" placeholder="Search trainings..." class="pl-8" v-model="search" />
                </div>
            </div>

            <div class="rounded-lg border">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr class="border-b">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Standard Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Max Hours</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">IRATA</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="training in trainings.data" :key="training.id" class="hover:bg-muted/50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    {{ training.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ formatPrice(training.price_standard) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ training.max_hours }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    {{ training.is_irata ? 'Yes' : 'No' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <div class="flex space-x-2">
                                        <Link :href="route('trainings.show', training.id)">
                                            <Button variant="outline" size="icon">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Link :href="route('trainings.edit', training.id)" v-if="userRoles.includes('Admin')">
                                            <Button variant="outline" size="icon">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="destructive"
                                            size="icon"
                                            @click="confirmDelete(training)"
                                            v-if="userRoles.includes('Admin')"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="trainings.data.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-sm">No trainings found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination :links="trainings.links" />
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Training</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete {{ trainingToDelete?.name }}? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="isDeleteDialogOpen = false">Cancel</Button>
                    <Button
                        variant="destructive"
                        @click="
                            () => {
                                router.delete(route('trainings.destroy', trainingToDelete?.id), {
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
