<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  modelValue?: string;
  id?: string;
  name?: string;
  placeholder?: string;
  rows?: number;
  disabled?: boolean;
  required?: boolean;
  class?: string;
}>();

const emit = defineEmits(['update:modelValue']);

const value = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
});

const classes = computed(() => {
  return [
    'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
    props.class,
  ].filter(Boolean).join(' ');
});
</script>

<template>
  <textarea
    :id="id"
    :name="name"
    :placeholder="placeholder"
    :rows="rows || 3"
    :disabled="disabled"
    :required="required"
    :class="classes"
    v-model="value"
  ></textarea>
</template>
