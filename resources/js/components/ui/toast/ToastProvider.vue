<script setup lang="ts">
import { Toast } from '@/components/ui/toast';
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { type SharedData } from '@/types';

const page = usePage<SharedData>();

const showSuccessToast = ref(false);
const showErrorToast = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// Watch for flash messages and show toasts
watch(() => page.props.flash, (newFlash) => {
  if (newFlash.success) {
    successMessage.value = newFlash.success;
    showSuccessToast.value = true;
  }

  if (newFlash.error) {
    errorMessage.value = newFlash.error;
    showErrorToast.value = true;
  }
}, { deep: true, immediate: true });

// Check for flash messages on mount
onMounted(() => {
  if (page.props.flash.success) {
    successMessage.value = page.props.flash.success;
    showSuccessToast.value = true;
  }

  if (page.props.flash.error) {
    errorMessage.value = page.props.flash.error;
    showErrorToast.value = true;
  }
});

const closeSuccessToast = () => {
  showSuccessToast.value = false;
};

const closeErrorToast = () => {
  showErrorToast.value = false;
};
</script>

<template>
  <Toast
    v-if="showSuccessToast && successMessage"
    :message="successMessage"
    type="success"
    :on-close="closeSuccessToast"
  />
  <Toast
    v-if="showErrorToast && errorMessage"
    :message="errorMessage"
    type="error"
    :on-close="closeErrorToast"
  />
</template>
