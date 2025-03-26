<script setup lang="ts">
import UserForm from './Form.vue';

interface User {
  id: number;
  name: string;
  email: string;
  created_at: string;
}

interface Props {
  user: User;
}

defineProps<Props>();
</script>

<template>
  <UserForm mode="show" :user="user" />
</template>
