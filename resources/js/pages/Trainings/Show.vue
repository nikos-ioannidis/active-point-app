<script setup lang="ts">
import Form from './Form.vue';

interface Training {
    id?: number;
    name: string;
    price_standard: number;
    max_hours: number;
    is_irata: boolean;
    created_at?: string;
    updated_at?: string;
}

defineProps<{
    training: Training;
}>();
</script>

<template>
    <Form mode="show" :training="training" />
</template>
