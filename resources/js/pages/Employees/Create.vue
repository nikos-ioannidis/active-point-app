<script setup lang="ts">
import EmployeeForm from './Form.vue';

interface User {
  id: number;
  name: string;
  email: string;
}

interface Props {
  users: User[];
  irataLevels: string[];
}

defineProps<Props>();
</script>

<template>
  <EmployeeForm mode="create" :users="users" :irata-levels="irataLevels" />
</template>
