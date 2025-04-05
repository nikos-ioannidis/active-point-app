<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    class?: string;
    showSystem?: boolean;
}

const props = defineProps<Props>();

console.log(props);

// containerClass
const containerClass = computed(() => {
    return props.class ? props.class : '';
});

const { appearance, updateAppearance } = useAppearance();

type Appearance = 'light' | 'dark' | 'system';

const tabs: { value: Appearance; Icon: any; label: string }[] = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
];

if (!props.showSystem) {
    tabs.push({ value: 'system' as Appearance, Icon: Monitor, label: 'System' });
}
</script>

<template>
    <div :class="['inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800', containerClass]" class="w-100 appearance-container">
        <button
            v-for="{ value, Icon, label } in tabs"
            :key="value"
            @click="updateAppearance(value)"
            :class="[
                'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
                appearance === value
                    ? 'bg-white shadow-sm dark:bg-neutral-700 dark:text-neutral-100'
                    : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
            ]"
        >
            <component :is="Icon" class="-ml-1 h-4 w-4" />
            <span class="ml-1.5 text-sm">{{ label }}</span>
        </button>
    </div>
</template>

<style scoped lang="scss">
.appearance-container {
    width: 100%;

    button {
        width: 100%;
    }
}
</style>
