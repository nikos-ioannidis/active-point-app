<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { PlusCircle, Pencil, Eye, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import Heading from '@/components/Heading.vue';
import { formatDate } from '@/lib/helpers';
import { ref } from 'vue';
import { type BreadcrumbItem } from '@/types';

interface User {
  id: number;
  name: string;
  email: string;
  role?: string;
  created_at: string;
}

interface Props {
  users: User[];
}

defineProps<Props>();
const userToDelete = ref<User | null>(null);
const isDeleteDialogOpen = ref(false);

const confirmDelete = (user: User) => {
  userToDelete.value = user;
  isDeleteDialogOpen.value = true;
};


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Users',
    href: '/users',
  },
];
</script>

<template>
  <Head title="Users" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col space-y-6 px-4 py-6">
      <div class="flex items-center justify-between">
        <Heading title="Users" description="Manage system users" />
        <Link :href="route('users.create')">
          <Button>
            <PlusCircle class="mr-2 h-4 w-4" />
            Add User
          </Button>
        </Link>
      </div>

      <div class="rounded-lg border">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-muted/50">
              <tr class="border-b">
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Email</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Role</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Created At</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr v-for="user in users" :key="user.id" class="hover:bg-muted/50">
                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                  {{ user.name }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm">
                  {{ user.email }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm">
                  {{ user.role || 'None' }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm">
                  {{ formatDate(user.created_at) }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm">
                  <div class="flex space-x-2">
                    <Link :href="route('users.show', user.id)">
                      <Button variant="outline" size="icon">
                        <Eye class="h-4 w-4" />
                      </Button>
                    </Link>
                    <Link :href="route('users.edit', user.id)">
                      <Button variant="outline" size="icon">
                        <Pencil class="h-4 w-4" />
                      </Button>
                    </Link>
                    <Button variant="destructive" size="icon" @click="confirmDelete(user)">
                      <Trash2 class="h-4 w-4" />
                    </Button>
                  </div>
                </td>
              </tr>
              <tr v-if="users.length === 0">
                <td colspan="5" class="px-6 py-4 text-center text-sm">No users found</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:open="isDeleteDialogOpen">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Delete User</DialogTitle>
          <DialogDescription>
            Are you sure you want to delete {{ userToDelete?.name }}? This action cannot be undone.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" @click="isDeleteDialogOpen = false">Cancel</Button>
          <Button
            variant="destructive"
            @click="() => {
              router.delete(route('users.destroy', userToDelete?.id), {
                onFinish: () => isDeleteDialogOpen = false
              });
            }"
          >
            Delete
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
