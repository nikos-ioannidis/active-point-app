<script setup lang="ts">
import WorkCategoryForm from './Form.vue';

interface WorkCategory {
  id: number;
  name: string;
  created_at: string;
  updated_at: string;
}

interface Props {
  workCategory: WorkCategory;
}

defineProps<Props>();
</script>

<template>
  <WorkCategoryForm mode="edit" :work-category="workCategory" />
</template>
