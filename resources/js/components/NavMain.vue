<script setup lang="ts">
import { Collapsible, CollapsibleContent } from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown, ChevronRight } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

const props = defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();

const openItems = ref<Record<string, boolean>>({});

// Function to check if an item has an active child
const hasActiveChild = (item: NavItem): boolean => {
    if (!item.children) return false;
    return item.children.some((child) => {
        return isItemActiveChild(child);
    });
};

// Initialize openItems based on current URL
const initializeOpenItems = () => {
    props.items.forEach((item) => {
        if (item.children && hasActiveChild(item)) {
            openItems.value[item.title] = true;
        }
    });
};

// Watch for URL changes to update openItems
watch(
    () => page.url,
    () => {
        initializeOpenItems();
    },
    { deep: true },
);

// Initialize on component mount
onMounted(() => {
    initializeOpenItems();
});

const toggleItem = (title: string) => {
    openItems.value[title] = !openItems.value[title];
};

const isItemActive = (item: NavItem) => {
    if (item.href === page.url) return true;
    if (item.children) {
        return item.children.some((child) => {
            return isItemActiveChild(child);
        });
    }
    return false;
};

const isItemActiveChild = (item: NavItem) => {
    return page.url.includes(item.href);
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Menu</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <!-- Regular menu item without children -->
                <SidebarMenuItem v-if="!item.children">
                    <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                        <Link :href="item.href">
                            <component :is="item.icon" class="h-4 w-4" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>

                <!-- Collapsible menu item with children -->
                <SidebarMenuItem v-else>
                    <Collapsible v-model:open="openItems[item.title]">
                        <SidebarMenuButton
                            as-child
                            :is-active="isItemActive(item)"
                            :tooltip="item.title"
                            @click="toggleItem(item.title)"
                            class="cursor-pointer justify-between"
                        >
                            <div class="flex w-full items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <component :is="item.icon" class="h-4 w-4" />
                                    <span>{{ item.title }}</span>
                                </div>
                                <ChevronDown v-if="openItems[item.title]" class="h-4 w-4" />
                                <ChevronRight v-else class="h-4 w-4" />
                            </div>
                        </SidebarMenuButton>

                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="child in item.children" :key="child.title">
                                    <SidebarMenuSubButton as-child :is-active="isItemActiveChild(child)">
                                        <Link :href="child.href">
                                            <span>{{ child.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </Collapsible>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
