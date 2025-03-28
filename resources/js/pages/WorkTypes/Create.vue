<script setup lang="ts">
import WorkTypeForm from './Form.vue';

interface WorkCategory {
  id: number;
  name: string;
}

interface Props {
  categories: WorkCategory[];
}

defineProps<Props>();
</script>

<template>
  <WorkTypeForm mode="create" :categories="categories" />
</template>
