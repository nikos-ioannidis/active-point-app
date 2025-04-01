<script setup lang="ts">
import VehicleForm from './Form.vue';

interface Vehicle {
    id: number;
    license_plate: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    vehicle: Vehicle;
}

defineProps<Props>();
</script>

<template>
  <VehicleForm mode="show" :vehicle="vehicle" />
</template>
