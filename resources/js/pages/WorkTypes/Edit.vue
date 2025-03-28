<script setup lang="ts">
import WorkTypeForm from './Form.vue';

interface WorkCategory {
  id: number;
  name: string;
}

interface WorkType {
  id: number;
  work_category_id: number;
  name: string;
  price_standard: number;
  price_gamesa: number;
  price_gamesa_abroad: number;
  max_hours: number;
  created_at: string;
  updated_at: string;
  work_category?: WorkCategory;
}

interface Props {
  workType: WorkType;
  categories: WorkCategory[];
}

defineProps<Props>();
</script>

<template>
  <WorkTypeForm mode="edit" :work-type="workType" :categories="categories" />
</template>
