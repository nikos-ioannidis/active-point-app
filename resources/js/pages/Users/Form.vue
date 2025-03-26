<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Pencil } from 'lucide-vue-next';

interface User {
    id?: number;
    name: string;
    email: string;
    created_at?: string;
}

interface Props {
    user?: User;
    mode: 'create' | 'edit' | 'show';
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    if (props.mode === 'create') {
        form.post(route('users.store'));
    } else if (props.mode === 'edit' && props.user?.id) {
        form.put(route('users.update', props.user.id));
    }
};

const formatDate = (dateString?: string) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString();
};

const getPageTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create User';
        case 'edit':
            return `Edit User: ${props.user?.name}`;
        case 'show':
            return 'User Details';
        default:
            return 'User';
    }
};

const getHeadingTitle = () => {
    switch (props.mode) {
        case 'create':
            return 'Create New User';
        case 'edit':
            return `Edit User`;
        case 'show':
            return 'User Details';
        default:
            return 'User';
    }
};

const getHeadingDescription = () => {
    switch (props.mode) {
        case 'create':
            return 'Add a new user to the system';
        case 'edit':
            return `Update information for ${props.user?.name}`;
        case 'show':
            return `View information for ${props.user?.name}`;
        default:
            return '';
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
    {
        title: getPageTitle(),
        href:
            props.mode === 'create'
                ? '/users/create'
                : props.user?.id
                  ? props.mode === 'edit'
                      ? `/users/${props.user.id}/edit`
                      : `/users/${props.user.id}`
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
                <Link :href="route('users.index')" class="text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-6 w-6" />
                </Link>
                <Heading :title="getHeadingTitle()" :description="getHeadingDescription()" />
            </div>

            <!-- Show mode -->
            <div v-if="isReadOnly && user" class="rounded-lg border p-6">
                <div class="grid gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Name</h3>
                        <p class="mt-1 text-lg font-semibold">{{ user.name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-muted-foreground">Email</h3>
                        <p class="mt-1 text-lg font-semibold">{{ user.email }}</p>
                    </div>
                    <div v-if="user.created_at">
                        <h3 class="text-sm font-medium text-muted-foreground">Created At</h3>
                        <p class="mt-1 text-lg font-semibold">{{ formatDate(user.created_at) }}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <Link :href="route('users.edit', user.id)">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit User
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Create/Edit mode -->
            <form v-else @submit.prevent="submit" class="space-y-6 rounded-lg border p-6">
                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" v-model="form.name" type="text" required placeholder="Full name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" required placeholder="Email address" />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">
                            {{ props.mode === 'edit' ? 'Password (leave blank to keep current password)' : 'Password' }}
                        </Label>
                        <Input id="password" v-model="form.password" type="password" :required="props.mode === 'create'" placeholder="Password" />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">Confirm Password</Label>
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            :required="props.mode === 'create' || form.password"
                            placeholder="Confirm password"
                        />
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ props.mode === 'create' ? 'Create User' : 'Update User' }}
                    </Button>
                    <Link :href="route('users.index')">
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
