<script setup lang="ts">
import { CheckCircle, AlertCircle, X } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

interface Props {
  message: string;
  type?: 'success' | 'error';
  duration?: number;
  onClose?: () => void;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'success',
  duration: 5000,
  onClose: () => {}
});

const visible = ref(true);
const isClosing = ref(false);

const close = () => {
  isClosing.value = true;
  setTimeout(() => {
    visible.value = false;
    props.onClose();
  }, 300); // Animation duration
};

onMounted(() => {
  if (props.duration > 0) {
    setTimeout(close, props.duration);
  }
});

const icon = computed(() => {
  return props.type === 'success' ? CheckCircle : AlertCircle;
});

const toastClasses = computed(() => {
  const baseClasses = 'fixed top-4 right-4 z-50 flex items-center gap-2 rounded-md p-4 shadow-md transition-all duration-300';
  const typeClasses = props.type === 'success'
    ? 'bg-green-50 text-green-800 border border-green-200 dark:bg-green-950 dark:text-green-200 dark:border-green-800'
    : 'bg-red-50 text-red-800 border border-red-200 dark:bg-red-950 dark:text-red-200 dark:border-red-800';
  const visibilityClasses = isClosing.value
    ? 'opacity-0 translate-x-4'
    : 'opacity-100 translate-x-0';

  return `${baseClasses} ${typeClasses} ${visibilityClasses}`;
});
</script>

<template>
  <Teleport to="body">
    <div v-if="visible" :class="toastClasses">
      <component :is="icon" class="h-5 w-5" />
      <span>{{ message }}</span>
      <button @click="close" class="ml-2 rounded-full p-1 hover:bg-black/10">
        <X class="h-4 w-4" />
      </button>
    </div>
  </Teleport>
</template>
