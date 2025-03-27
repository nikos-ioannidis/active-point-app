<script setup lang="ts">
import UserForm from './Form.vue';

interface User {
  id: number;
  name: string;
  email: string;
  role?: string;
}

interface Role {
  id: number;
  name: string;
}

interface Props {
  user: User;
  roles: Role[];
}

defineProps<Props>();
</script>

<template>
  <UserForm mode="edit" :user="user" :roles="roles" />
</template>
