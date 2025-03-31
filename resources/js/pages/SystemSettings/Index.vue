<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface SettingConfig {
    key: string;
    label: string;
    type: string;
    default: any;
    group: string;
    validation: string;
    options?: { value: string; label: string }[];
    source?: {
        model: string;
        value_field: string;
        label_field: string;
    };
}

interface Props {
    groups: string[];
    settingsConfig: SettingConfig[];
    settingsValues: Record<string, any>;
    dynamicData?: Record<string, { value: any; label: string }[]>;
}

const props = defineProps<Props>();

// Create form with all settings values
const form = useForm(props.settingsValues);

// Active tab for settings groups
const activeGroup = ref(props.groups[0]);


// Group settings by their group
const groupedSettings = computed(() => {
    const grouped: Record<string, SettingConfig[]> = {};

    props.groups.forEach(group => {
        grouped[group] = props.settingsConfig.filter(setting => setting.group === group);
    });

    return grouped;
});

// Format group name for display
const formatGroupName = (group: string) => {
    return group.charAt(0).toUpperCase() + group.slice(1);
};

// Submit the form
const submit = () => {
    form.post(route('system-settings.update'));
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'System Settings',
        href: '/system-settings',
    },
];
</script>

<template>
    <Head title="System Settings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-6 px-4 py-6">
            <Heading title="System Settings" description="Configure system-wide settings" />

            <div class="flex space-x-6">
                <!-- Tabs for settings groups -->
                <div class="w-64 shrink-0">
                    <div class="rounded-lg border">
                        <div class="divide-y">
                            <button
                                v-for="group in groups"
                                :key="group"
                                @click="activeGroup = group"
                                class="w-full px-4 py-3 text-left text-sm font-medium transition-colors"
                                :class="{
                                    'bg-muted': activeGroup === group,
                                    'hover:bg-muted/50': activeGroup !== group,
                                }"
                            >
                                {{ formatGroupName(group) }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Settings form -->
                <div class="flex-1">
                    <form @submit.prevent="submit" class="space-y-6 rounded-lg border p-6">
                        <div v-for="group in groups" :key="group" v-show="activeGroup === group">
                            <div class="grid gap-6 md:grid-cols-2">
                                <template v-for="setting in groupedSettings[group]" :key="setting.key">
                                    <!-- Text input -->
                                    <div v-if="setting.type === 'text'" class="grid gap-2">
                                        <Label :for="setting.key">{{ setting.label }}</Label>
                                        <Input :id="setting.key" v-model="form[setting.key]" type="text" />
                                        <InputError :message="form.errors[setting.key]" />
                                    </div>

                                    <!-- Email input -->
                                    <div v-else-if="setting.type === 'email'" class="grid gap-2">
                                        <Label :for="setting.key">{{ setting.label }}</Label>
                                        <Input :id="setting.key" v-model="form[setting.key]" type="email" />
                                        <InputError :message="form.errors[setting.key]" />
                                    </div>

                                    <!-- Number input -->
                                    <div v-else-if="setting.type === 'number'" class="grid gap-2">
                                        <Label :for="setting.key">{{ setting.label }}</Label>
                                        <Input :id="setting.key" v-model="form[setting.key]" type="number" step="any" />
                                        <InputError :message="form.errors[setting.key]" />
                                    </div>

                                    <!-- Textarea -->
                                    <div v-else-if="setting.type === 'textarea'" class="grid gap-2">
                                        <Label :for="setting.key">{{ setting.label }}</Label>
                                        <Textarea :id="setting.key" v-model="form[setting.key]" />
                                        <InputError :message="form.errors[setting.key]" />
                                    </div>

                                    <!-- Boolean/Checkbox -->
                                    <div v-else-if="setting.type === 'boolean'" class="grid gap-2">
                                        <div class="flex items-center space-x-2">
                                            <Checkbox :id="setting.key" v-model="form[setting.key]" />
                                            <Label :for="setting.key">{{ setting.label }}</Label>
                                        </div>
                                        <InputError :message="form.errors[setting.key]" />
                                    </div>

                                    <!-- Static Select -->
                                    <div v-else-if="setting.type === 'select_static'" class="grid gap-2">
                                        <Label :for="setting.key">{{ setting.label }}</Label>
                                        <select
                                            :id="setting.key"
                                            v-model="form[setting.key]"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <option v-for="option in setting.options" :key="option.value" :value="option.value">
                                                {{ option.label }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors[setting.key]" />
                                    </div>

                                    <!-- Dynamic Select -->
                                    <div v-else-if="setting.type === 'select_dynamic'" class="grid gap-2">
                                        <Label :for="setting.key">{{ setting.label }}</Label>
                                        <select
                                            :id="setting.key"
                                            v-model="form[setting.key]"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <option value="">Select an option</option>
                                            <option
                                                v-for="option in dynamicData?.[setting.key] || []"
                                                :key="option.value"
                                                :value="option.value"
                                            >
                                                {{ option.label }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors[setting.key]" />
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">Save System Settings</Button>
                            <div v-show="form.recentlySuccessful" class="text-sm text-green-600">Saved.</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
