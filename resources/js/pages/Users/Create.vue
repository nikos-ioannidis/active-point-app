<script setup lang="ts">
import UserForm from './Form.vue';

interface Role {
  id: number;
  name: string;
}

interface Props {
  roles: Role[];
}

defineProps<Props>();
</script>

<template>
  <UserForm mode="create" :roles="roles" />
</template>
