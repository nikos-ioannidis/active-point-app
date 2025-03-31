<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatPrice } from '@/lib/helpers';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Pencil } from 'lucide-vue-next';

interface WorkCategory {
    id: number;
    name: string;
}

interface WorkType {
    id?: number;
    work_category_id: number;
    name: string;
    price_standard: number;
    price_gamesa: number;
    price_gamesa_abroad: number;
    max_hours: number;
    created_at?: string;
    updated_at?: string;
    work_category?: WorkCategory;
}

interface Props {
    workType?: WorkType;
    categories?: WorkCategory[];
    mode: 'create' | 'edit' | 'show';
}

const props = defineProps<Props>();

const form = useForm({
    work_category_id: props.workType?.work_category_id || '',
    name: props.workType?.name || '',
    price_standard: props.workType?.price_standard || 0,
    price_gamesa: props.workType?.price_gamesa ?? null,
    price_gamesa_abroad: props.workType?.price_gamesa_abroad ?? null,
    max_hours: props.workType?.max_hours || 0,
});

const submit = () => {
    if (props.mode === 'create') {
        form.post(route('work-types.store'));
    } else if (props.mode === 'edit' && props.workType?.id) {
        form.put(route('work-types.update', props.workType.id));
    }
};

const getPageTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create Work Type';
        case 'edit':
            return `Edit Work Type: ${props.workType?.name}`;
        case 'show':
            return 'Work Type Details';
        default:
            return 'Work Type';
    }
};

const getHeadingTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create New Work Type';
        case 'edit':
            return `Edit Work Type`;
        case 'show':
            return 'Work Type Details';
        default:
            return 'Work Type';
    }
};

const getHeadingDescription = () => {
    switch (props.mode) {
        case 'create':
            return 'Add a new work type to the system';
        case 'edit':
            return `Update information for ${props.workType?.name}`;
        case 'show':
            return `View information for ${props.workType?.name}`;
        default:
            return '';
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Work Types',
        href: '/work-types',
    },
    {
        title: getPageTitle(),
        href:
            props.mode === 'create'
                ? '/work-types/create'
                : props.workType?.id
                  ? props.mode === 'edit'
                      ? `/work-types/${props.workType.id}/edit`
                      : `/work-types/${props.workType.id}`
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
                <Link :href="route('work-types.index')" class="text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-6 w-6" />
                </Link>
                <Heading :title="getHeadingTitle()" :description="getHeadingDescription()" />
            </div>

            <!-- Show mode -->
            <div v-if="isReadOnly && workType" class="rounded-lg border p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Name</h3>
                        <p class="mt-1 text-lg font-semibold">{{ workType.name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Category</h3>
                        <p class="mt-1 text-lg font-semibold">{{ workType.work_category?.name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Standard Price</h3>
                        <p class="mt-1 text-lg font-semibold">{{ formatPrice(workType.price_standard) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Gamesa Price</h3>
                        <p class="mt-1 text-lg font-semibold">
                            {{ formatPrice(workType.price_gamesa) }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Gamesa Abroad Price</h3>
                        <p class="mt-1 text-lg font-semibold">
                            {{ formatPrice(workType.price_gamesa_abroad) }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Max Hours</h3>
                        <p class="mt-1 text-lg font-semibold">{{ workType.max_hours }}</p>
                    </div>
                    <!-- <div v-if="workType.created_at">
                        <h3 class="text-sm font-medium text-muted-foreground">Created At</h3>
                        <p class="mt-1 text-lg font-semibold">{{ formatDate(workType.created_at) }}</p>
                    </div> -->
                </div>
                <div class="mt-6 flex justify-end">
                    <Link :href="route('work-types.edit', workType.id)">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit Work Type
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Create/Edit mode -->
            <form v-else @submit.prevent="submit" class="space-y-6 rounded-lg border p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="work_category_id">Category</Label>
                        <select
                            id="work_category_id"
                            v-model="form.work_category_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            required
                        >
                            <option value="" disabled>Select a category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.work_category_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Work Type Name</Label>
                        <Input id="name" v-model="form.name" type="text" required placeholder="Work type name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="price_standard">Standard Price</Label>
                        <Input id="price_standard" v-model="form.price_standard" type="number" step="0.01" min="0" required placeholder="0.00" />
                        <InputError :message="form.errors.price_standard" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="price_gamesa">Gamesa Price</Label>
                        <Input id="price_gamesa" v-model="form.price_gamesa!" type="number" step="0.01" min="0" placeholder="0.00" />
                        <InputError :message="form.errors.price_gamesa" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="price_gamesa_abroad">Gamesa Abroad Price</Label>
                        <Input id="price_gamesa_abroad" v-model="form.price_gamesa_abroad!" type="number" step="0.01" min="0" placeholder="0.00" />
                        <InputError :message="form.errors.price_gamesa_abroad" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="max_hours">Max Hours</Label>
                        <Input id="max_hours" v-model="form.max_hours" type="number" min="0" required placeholder="0" />
                        <InputError :message="form.errors.max_hours" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ props.mode === 'create' ? 'Create Work Type' : 'Update Work Type' }}
                    </Button>
                    <Link :href="route('work-types.index')">
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
