<script setup lang="ts">
import WorkJobForm from './Form.vue';

interface WorkJob {
    id: number;
    code: string;
    description: string;
    client_name: string;
    client_id: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    workJob: WorkJob;
}

defineProps<Props>();
</script>

<template>
  <WorkJobForm mode="edit" :work-job="workJob" />
</template>
