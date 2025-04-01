<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

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
    form.post(route('employees.work-types.update', props.employee.id));
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
        title: 'Employees',
        href: '/employees',
    },
    {
        title: props.employee.employee_name,
        href: `/employees/${props.employee.id}`,
    },
    {
        title: 'Work Types',
        href: `/employees/${props.employee.id}/work-types`,
    },
];
</script>

<template>
    <Head :title="`Work Types - ${employee.employee_name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <div class="flex items-start space-x-2">
                <Link :href="route('employees.show', employee.id)" class="text-muted-foreground hover:text-foreground">
                    <ArrowLeft class="h-6 w-6" />
                </Link>
                <Heading
                    :title="`Work Types for ${employee.employee_name}`"
                    description="Assign one work type for each category or leave it empty"
                />
            </div>

            <form @submit.prevent="submit" class="space-y-6 rounded-lg border p-6">
                <div>
                    <h3 class="text-lg font-medium">Work Type Assignments</h3>
                    <p class="text-sm text-muted-foreground">
                        Select one work type for each category or leave it empty if the employee doesn't work in that category.
                    </p>
                </div>
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
                <div class="flex justify-between">
                    <div class="flex space-x-2">
                        <Button type="button" variant="outline" @click="reset">Reset to last changes</Button>
                        <Link :href="route('employees.show', employee.id)">
                            <Button type="button" variant="outline">Back to Employee</Button>
                        </Link>
                    </div>
                    <Button type="submit" :disabled="form.processing">Save Assignments</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
