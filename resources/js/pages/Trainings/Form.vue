<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatPrice } from '@/lib/helpers';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Pencil } from 'lucide-vue-next';

interface Training {
    id?: number;
    name: string;
    price_standard: number;
    max_hours: number;
    is_irata: boolean;
    created_at?: string;
    updated_at?: string;
}

interface Props {
    training?: Training;
    mode: 'create' | 'edit' | 'show';
}

const props = defineProps<Props>();

const form = useForm({
    name: props.training?.name || '',
    price_standard: props.training?.price_standard || 0,
    max_hours: props.training?.max_hours || 0,
    is_irata: props.training?.is_irata || false,
});

const submit = () => {
    if (props.mode === 'create') {
        form.post(route('trainings.store'));
    } else if (props.mode === 'edit' && props.training?.id) {
        form.put(route('trainings.update', props.training.id));
    }
};

const getPageTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create Training';
        case 'edit':
            return `Edit Training: ${props.training?.name}`;
        case 'show':
            return 'Training Details';
        default:
            return 'Training';
    }
};

const getHeadingTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create New Training';
        case 'edit':
            return `Edit Training`;
        case 'show':
            return 'Training Details';
        default:
            return 'Training';
    }
};

const getHeadingDescription = () => {
    switch (props.mode) {
        case 'create':
            return 'Add a new training to the system';
        case 'edit':
            return `Update information for ${props.training?.name}`;
        case 'show':
            return `View information for ${props.training?.name}`;
        default:
            return '';
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Trainings',
        href: '/trainings',
    },
    {
        title: getPageTitle(),
        href:
            props.mode === 'create'
                ? '/trainings/create'
                : props.training?.id
                  ? props.mode === 'edit'
                      ? `/trainings/${props.training.id}/edit`
                      : `/trainings/${props.training.id}`
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
                <Link :href="route('trainings.index')" class="text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-6 w-6" />
                </Link>
                <Heading :title="getHeadingTitle()" :description="getHeadingDescription()" />
            </div>

            <!-- Show mode -->
            <div v-if="isReadOnly && training" class="rounded-lg border p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Name</h3>
                        <p class="mt-1 text-lg font-semibold">{{ training.name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Standard Price</h3>
                        <p class="mt-1 text-lg font-semibold">{{ formatPrice(training.price_standard) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Max Hours</h3>
                        <p class="mt-1 text-lg font-semibold">{{ training.max_hours }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">IRATA</h3>
                        <p class="mt-1 text-lg font-semibold">{{ training.is_irata ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <Link :href="route('trainings.edit', training.id)">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit Training
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Create/Edit mode -->
            <form v-else @submit.prevent="submit" class="space-y-6 rounded-lg border p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="name">Training Name</Label>
                        <Input id="name" v-model="form.name" type="text" required placeholder="Training name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="price_standard">Standard Price</Label>
                        <Input id="price_standard" v-model="form.price_standard" type="number" step="0.01" min="0" required placeholder="0.00" />
                        <InputError :message="form.errors.price_standard" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="max_hours">Max Hours</Label>
                        <Input id="max_hours" v-model="form.max_hours" type="number" min="0" required placeholder="0" />
                        <InputError :message="form.errors.max_hours" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="is_irata">IRATA</Label>
                        <div class="flex items-center space-x-2">
                            <Checkbox id="is_irata" v-model="form.is_irata" />
                            <Label for="is_irata">{{ form.is_irata ? 'Yes' : 'No' }}</Label>
                        </div>
                        <InputError :message="form.errors.is_irata" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ props.mode === 'create' ? 'Create Training' : 'Update Training' }}
                    </Button>
                    <Link :href="route('trainings.index')">
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
