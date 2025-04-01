<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

interface WorkType {
    id: number;
    name: string;
    work_category_id: number;
}

interface WorkCategory {
    id: number;
    name: string;
    work_types: WorkType[];
}

interface EmployeeWorkType {
    id: number;
    employee_id: number;
    work_category_id: number;
    work_type_id: number | null;
}

interface Employee {
    id: number;
    employee_name: string;
    employee_code: string;
}

interface Props {
    employee: Employee;
    categories: WorkCategory[];
    selections: Record<number, EmployeeWorkType>;
}

const props = defineProps<Props>();

// Initialize form with current selections
const form = useForm({
    selections: [] as Array<{
        work_category_id: number;
        work_type_id: number | null;
    }>,
});

// Initialize selections from props
const initializeSelections = () => {
    const initialSelections = props.categories.map(category => {
        const existingSelection = props.selections[category.id];
        return {
            work_category_id: category.id,
            work_type_id: existingSelection ? existingSelection.work_type_id : null,
        };
    });

    form.selections = initialSelections;
};

// Initialize on component mount
initializeSelections();

// Handle selection change
const updateSelection = (categoryId: number, typeId: string | null): void => {
    const index = form.selections.findIndex(s => s.work_category_id === categoryId);
    if (index !== -1) {
        form.selections[index].work_type_id = typeId ? parseInt(typeId) : null;
    }
};

// Submit the form
const submit = () => {
    form.post(route('employee-work-types.update', props.employee.id));
};

// Reset the form
const reset = () => {
    initializeSelections();
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Employee Work Types',
        href: '/employee-work-types',
    },
    {
        title: props.employee.employee_name,
        href: `/employee-work-types/${props.employee.id}`,
    },
];
</script>

<template>
    <Head :title="`Edit Work Types - ${employee.employee_name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container py-6">
            <Heading
                :title="`Work Types for ${employee.employee_name}`"
                description="Assign one work type for each category or leave it empty"
            />

            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Work Type Assignments</CardTitle>
                        <CardDescription>
                            Select one work type for each category or leave it empty if the employee doesn't work in that category.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div v-for="category in categories" :key="category.id" class="space-y-2">
                                <Label :for="`category-${category.id}`">{{ category.name }}</Label>
                                <select
                                    :id="`category-${category.id}`"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :value="form.selections.find(s => s.work_category_id === category.id)?.work_type_id?.toString() || ''"
                                    @change="(e) => updateSelection(category.id, (e.target as HTMLSelectElement).value || null)"
                                >
                                    <option value="">None</option>
                                    <option
                                        v-for="type in category.work_types"
                                        :key="type.id"
                                        :value="type.id.toString()"
                                    >
                                        {{ type.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-between">
                        <div class="flex space-x-2">
                            <Button type="button" variant="outline" @click="reset">Reset</Button>
                            <Link :href="route('employee-work-types.index')">
                                <Button type="button" variant="outline">Back to List</Button>
                            </Link>
                        </div>
                        <Button type="submit" :disabled="form.processing">Save Assignments</Button>
                    </CardFooter>
                </Card>
            </form>
        </div>
    </AppLayout>
</template>
