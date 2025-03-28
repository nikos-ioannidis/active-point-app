<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { formatDate } from '@/lib/helpers';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, PlusCircle } from 'lucide-vue-next';

interface WorkType {
    id: number;
    name: string;
    price_standard: number;
    price_gamesa: number;
    price_gamesa_abroad: number;
    max_hours: number;
}

interface WorkCategory {
    id?: number;
    name: string;
    created_at?: string;
    updated_at?: string;
    work_types?: WorkType[];
}

interface Props {
    workCategory?: WorkCategory;
    mode: 'create' | 'edit' | 'show';
}

const props = defineProps<Props>();

const form = useForm({
    name: props.workCategory?.name || '',
});

const submit = () => {
    if (props.mode === 'create') {
        form.post(route('work-categories.store'));
    } else if (props.mode === 'edit' && props.workCategory?.id) {
        form.put(route('work-categories.update', props.workCategory.id));
    }
};

const getPageTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create Work Category';
        case 'edit':
            return `Edit Work Category: ${props.workCategory?.name}`;
        case 'show':
            return 'Work Category Details';
        default:
            return 'Work Category';
    }
};

const getHeadingTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create New Work Category';
        case 'edit':
            return `Edit Work Category`;
        case 'show':
            return 'Work Category Details';
        default:
            return 'Work Category';
    }
};

const getHeadingDescription = () => {
    switch (props.mode) {
        case 'create':
            return 'Add a new work category to the system';
        case 'edit':
            return `Update information for ${props.workCategory?.name}`;
        case 'show':
            return `View information for ${props.workCategory?.name}`;
        default:
            return '';
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Work Categories',
        href: '/work-categories',
    },
    {
        title: getPageTitle(),
        href:
            props.mode === 'create'
                ? '/work-categories/create'
                : props.workCategory?.id
                  ? props.mode === 'edit'
                      ? `/work-categories/${props.workCategory.id}/edit`
                      : `/work-categories/${props.workCategory.id}`
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
                <Link :href="route('work-categories.index')" class="text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-6 w-6" />
                </Link>
                <Heading :title="getHeadingTitle()" :description="getHeadingDescription()" />
            </div>

            <!-- Show mode -->
            <div v-if="isReadOnly && workCategory" class="rounded-lg border p-6">
                <div class="grid gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Name</h3>
                        <p class="mt-1 text-lg font-semibold">{{ workCategory.name }}</p>
                    </div>
                    <div v-if="workCategory.created_at">
                        <h3 class="text-sm font-medium text-muted-foreground">Created At</h3>
                        <p class="mt-1 text-lg font-semibold">{{ formatDate(workCategory.created_at) }}</p>
                    </div>
                </div>

                <!-- Work Types List -->
                <div v-if="workCategory.work_types && workCategory.work_types.length > 0" class="mt-8">
                    <h3 class="mb-4 text-lg font-semibold">Work Types</h3>
                    <div class="rounded-lg border">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-muted/50">
                                    <tr class="border-b">
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Standard Price</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Gamesa Price</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Gamesa Abroad Price</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Max Hours</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <tr v-for="workType in workCategory.work_types" :key="workType.id" class="hover:bg-muted/50">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                            {{ workType.name }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            {{ workType.price_standard }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            {{ workType.price_gamesa }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            {{ workType.price_gamesa_abroad }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            {{ workType.max_hours }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div v-else class="mt-8">
                    <p class="text-muted-foreground">No work types associated with this category.</p>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <Link :href="route('work-categories.edit', workCategory.id)">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit Category
                        </Button>
                    </Link>
                    <Link :href="route('work-types.create', { category: workCategory.id })">
                        <Button variant="outline">
                            <PlusCircle class="mr-2 h-4 w-4" />
                            Add Work Type
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Create/Edit mode -->
            <form v-else @submit.prevent="submit" class="space-y-6 rounded-lg border p-6">
                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Category Name</Label>
                        <Input id="name" v-model="form.name" type="text" required placeholder="Category name" />
                        <InputError :message="form.errors.name" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ props.mode === 'create' ? 'Create Category' : 'Update Category' }}
                    </Button>
                    <Link :href="route('work-categories.index')">
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
