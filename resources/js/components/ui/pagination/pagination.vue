<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

interface Props {
  links: PaginationLink[];
}

const props = defineProps<Props>();

// Function to clean the label from HTML entities
const cleanLabel = (label: string) => {
  return label.replace(/&laquo;/g, '').replace(/&raquo;/g, '');
};
</script>

<template>
  <div v-if="links.length > 3" class="flex items-center justify-center space-x-1 py-4">
    <template v-for="(link, i) in links" :key="i">
      <!-- Previous page link -->
      <Link
        v-if="i === 0 && link.url"
        :href="link.url"
        class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
        aria-label="Go to previous page"
      >
        <ChevronLeft class="h-4 w-4" />
      </Link>
      <span
        v-else-if="i === 0"
        class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background text-sm font-medium text-muted-foreground ring-offset-background transition-colors disabled:pointer-events-none disabled:opacity-50"
      >
        <ChevronLeft class="h-4 w-4" />
      </span>

      <!-- Page number links (skip first and last which are prev/next) -->
      <Link
        v-if="i > 0 && i < links.length - 1 && link.url"
        :href="link.url"
        class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-md border border-input px-3 py-0 text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
        :class="{ 'bg-primary text-primary-foreground hover:bg-primary hover:text-primary-foreground': link.active }"
        :aria-label="`Go to page ${cleanLabel(link.label)}`"
      >
        {{ cleanLabel(link.label) }}
      </Link>
      <span
        v-else-if="i > 0 && i < links.length - 1"
        class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-md border border-input bg-background px-3 py-0 text-sm font-medium text-muted-foreground ring-offset-background transition-colors disabled:pointer-events-none disabled:opacity-50"
      >
        {{ cleanLabel(link.label) }}
      </span>

      <!-- Next page link -->
      <Link
        v-if="i === links.length - 1 && link.url"
        :href="link.url"
        class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
        aria-label="Go to next page"
      >
        <ChevronRight class="h-4 w-4" />
      </Link>
      <span
        v-else-if="i === links.length - 1"
        class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background text-sm font-medium text-muted-foreground ring-offset-background transition-colors disabled:pointer-events-none disabled:opacity-50"
      >
        <ChevronRight class="h-4 w-4" />
      </span>
    </template>
  </div>
</template>
