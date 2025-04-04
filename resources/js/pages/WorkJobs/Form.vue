<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Pencil } from 'lucide-vue-next';

interface WorkJob {
    id?: number;
    code: string;
    description: string;
    client_name: string;
    client_id: string;
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
}

interface Props {
    workJob?: WorkJob;
    mode: 'create' | 'edit' | 'show';
}

const props = defineProps<Props>();

const form = useForm({
    code: props.workJob?.code || '',
    description: props.workJob?.description || '',
    client_name: props.workJob?.client_name || '',
    client_id: props.workJob?.client_id || '',
    is_active: props.workJob?.is_active ?? true,
});

const submit = () => {
    if (props.mode === 'create') {
        form.post(route('work-jobs.store'));
    } else if (props.mode === 'edit' && props.workJob?.id) {
        form.put(route('work-jobs.update', props.workJob.id as number));
    }
};

const getPageTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create Job';
        case 'edit':
            return `Edit Job: ${props.workJob?.code}`;
        case 'show':
            return 'Job Details';
        default:
            return 'Job';
    }
};

const getHeadingTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create New Job';
        case 'edit':
            return `Edit Job`;
        case 'show':
            return 'Job Details';
        default:
            return 'Job';
    }
};

const getHeadingDescription = () => {
    switch (props.mode) {
        case 'create':
            return 'Add a new job to the system';
        case 'edit':
            return `Update information for ${props.workJob?.code}`;
        case 'show':
            return `View information for ${props.workJob?.code}`;
        default:
            return '';
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Jobs',
        href: '/work-jobs',
    },
    {
        title: getPageTitle(),
        href:
            props.mode === 'create'
                ? '/work-jobs/create'
                : props.workJob?.id
                  ? props.mode === 'edit'
                      ? `/work-jobs/${props.workJob.id as number}/edit`
                      : `/work-jobs/${props.workJob.id as number}`
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
                <Link :href="route('work-jobs.index')" class="text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-6 w-6" />
                </Link>
                <Heading :title="getHeadingTitle()" :description="getHeadingDescription()" />
            </div>

            <!-- Show mode -->
            <div v-if="isReadOnly && workJob" class="rounded-lg border p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Job Code</h3>
                        <p class="mt-1 text-lg font-semibold">{{ workJob.code }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Status</h3>
                        <p class="mt-1 text-lg font-semibold">
                            <span
                                :class="{
                                    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium': true,
                                    'bg-green-100 text-green-800': workJob.is_active,
                                    'bg-red-100 text-red-800': !workJob.is_active,
                                }"
                            >
                                {{ workJob.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Description</h3>
                        <p class="mt-1 text-lg">{{ workJob.description }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Client Name</h3>
                        <p class="mt-1 text-lg">{{ workJob.client_name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Client ID</h3>
                        <p class="mt-1 text-lg">{{ workJob.client_id }}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <Link :href="route('work-jobs.edit', workJob.id as number)">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit Job
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Create/Edit mode -->
            <form v-else @submit.prevent="submit" class="space-y-6 rounded-lg border p-6">
                <div class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="code">Job Code</Label>
                        <Input
                            id="code"
                            v-model="form.code"
                            type="text"
                            required
                            placeholder="Enter job code"
                        />
                        <InputError :message="form.errors.code" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Description</Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            required
                            placeholder="Enter job description"
                            :rows="4"
                        />
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="client_name">Client Name</Label>
                        <Input
                            id="client_name"
                            v-model="form.client_name"
                            type="text"
                            required
                            placeholder="Enter client name"
                        />
                        <InputError :message="form.errors.client_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="client_id">Client ID</Label>
                        <Input
                            id="client_id"
                            v-model="form.client_id"
                            type="text"
                            required
                            placeholder="Enter client ID"
                        />
                        <InputError :message="form.errors.client_id" />
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox id="is_active" v-model:checked="form.is_active" />
                        <Label for="is_active">Active</Label>
                        <InputError :message="form.errors.is_active" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ props.mode === 'create' ? 'Create Job' : 'Update Job' }}
                    </Button>
                    <Link :href="route('work-jobs.index')">
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
