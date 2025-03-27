<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="rounded-lg border p-6">
                <p class="text-lg">Welcome to the Active Point application</p>
                <p class="mt-4">Please wait until you will be assigned to a role</p>
            </div>
        </div>
    </AppLayout>
</template>
