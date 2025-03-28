<script setup lang="ts">
import EmployeeForm from './Form.vue';

interface User {
  id: number;
  name: string;
  email: string;
}

interface Employee {
  id: number;
  user_id: number | null;
  employee_code: string;
  employee_name: string;
  job_title: string;
  phone_number: string;
  is_active: boolean;
  owns_equipment: boolean;
  irata_level: string;
  created_at: string;
  updated_at: string;
  user?: User;
}

interface Props {
  employee: Employee;
  irataLevels: string[];
}

defineProps<Props>();
</script>

<template>
  <EmployeeForm mode="show" :employee="employee" :irata-levels="irataLevels" />
</template>
