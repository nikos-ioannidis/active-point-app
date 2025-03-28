<script setup lang="ts">
import WorkCategoryForm from './Form.vue';

interface WorkType {
  id: number;
  name: string;
  price_standard: number;
  price_gamesa: number;
  price_gamesa_abroad: number;
  max_hours: number;
}

interface WorkCategory {
  id: number;
  name: string;
  created_at: string;
  updated_at: string;
  work_types: WorkType[];
}

interface Props {
  workCategory: WorkCategory;
}

defineProps<Props>();
</script>

<template>
  <WorkCategoryForm mode="show" :work-category="workCategory" />
</template>
