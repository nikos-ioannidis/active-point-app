<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Pencil } from 'lucide-vue-next';

interface Vehicle {
    id?: number;
    license_plate: string;
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
}

interface Props {
    vehicle?: Vehicle;
    mode: 'create' | 'edit' | 'show';
}

const props = defineProps<Props>();

const form = useForm({
    license_plate: props.vehicle?.license_plate || '',
    is_active: props.vehicle?.is_active ?? true,
});

const submit = () => {
    if (props.mode === 'create') {
        form.post(route('vehicles.store'));
    } else if (props.mode === 'edit' && props.vehicle?.id) {
        form.put(route('vehicles.update', props.vehicle.id));
    }
};

const getPageTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create Vehicle';
        case 'edit':
            return `Edit Vehicle: ${props.vehicle?.license_plate}`;
        case 'show':
            return 'Vehicle Details';
        default:
            return 'Vehicle';
    }
};

const getHeadingTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create New Vehicle';
        case 'edit':
            return `Edit Vehicle`;
        case 'show':
            return 'Vehicle Details';
        default:
            return 'Vehicle';
    }
};

const getHeadingDescription = () => {
    switch (props.mode) {
        case 'create':
            return 'Add a new vehicle to the system';
        case 'edit':
            return `Update information for ${props.vehicle?.license_plate}`;
        case 'show':
            return `View information for ${props.vehicle?.license_plate}`;
        default:
            return '';
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Vehicles',
        href: '/vehicles',
    },
    {
        title: getPageTitle(),
        href:
            props.mode === 'create'
                ? '/vehicles/create'
                : props.vehicle?.id
                  ? props.mode === 'edit'
                      ? `/vehicles/${props.vehicle.id}/edit`
                      : `/vehicles/${props.vehicle.id}`
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
                <Link :href="route('vehicles.index')" class="text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-6 w-6" />
                </Link>
                <Heading :title="getHeadingTitle()" :description="getHeadingDescription()" />
            </div>

            <!-- Show mode -->
            <div v-if="isReadOnly && vehicle" class="rounded-lg border p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">License Plate</h3>
                        <p class="mt-1 text-lg font-semibold">{{ vehicle.license_plate }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Status</h3>
                        <p class="mt-1 text-lg font-semibold">
                            <span
                                :class="{
                                    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium': true,
                                    'bg-green-100 text-green-800': vehicle.is_active,
                                    'bg-red-100 text-red-800': !vehicle.is_active,
                                }"
                            >
                                {{ vehicle.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <Link :href="route('vehicles.edit', vehicle.id)">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit Vehicle
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Create/Edit mode -->
            <form v-else @submit.prevent="submit" class="space-y-6 rounded-lg border p-6">
                <div class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="license_plate">License Plate</Label>
                        <Input
                            id="license_plate"
                            v-model="form.license_plate"
                            type="text"
                            required
                            placeholder="Enter license plate"
                        />
                        <InputError :message="form.errors.license_plate" />
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox id="is_active" v-model:checked="form.is_active" />
                        <Label for="is_active">Active</Label>
                        <InputError :message="form.errors.is_active" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ props.mode === 'create' ? 'Create Vehicle' : 'Update Vehicle' }}
                    </Button>
                    <Link :href="route('vehicles.index')">
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
