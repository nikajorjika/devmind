<!-- WorkspaceSwitcher.vue -->
<script setup lang="ts">
import { ChevronsUpDown, Plus } from 'lucide-vue-next';
import { ref, watch } from 'vue';

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuShortcut,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';

/** Keep your Workspace shape: { domain: string; avatar: string; name: string } */
import type { Workspace } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import SwitchWorkspaceController from '@/actions/App/Http/Controllers/Workspace/SwitchWorkspaceController';

const emit = defineEmits<{
    /** same as your previous component */
    (e: 'change', value: Workspace): void;
    (e: 'update:selectedWorkspace', value: Workspace): void;
    /** optional: emit when "Add workspace" is clicked */
    (e: 'add'): void;
}>();

const page = usePage();
const workspaces = page.props.auth.workspaces;
const defaultWorkspace = page.props.currentWorkspace;

const { isMobile } = useSidebar();
const selectedWorkspace = ref<Workspace | null>(defaultWorkspace);

/**
 * CHANGE: replaced watchEffect with watch on the prop only,
 * so user selection won't be immediately reverted by a reactive loop.
 */
watch(
    () => defaultWorkspace,
    (val) => {
        selectedWorkspace.value = val;
    },
    { immediate: true },
);

function selectWorkspace(ws: Workspace) {
    selectedWorkspace.value = ws;
    router.post(
        SwitchWorkspaceController.url(),
        { workspace_id: ws.id },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );

    emit('change', ws);
    emit('update:selectedWorkspace', ws);
}

function addWorkspace() {
    emit('add');
}
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <!-- CHANGE: use kebab-case prop `as-child` (Vue templates) -->
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    >
                        <div
                            class="flex aspect-square size-8 items-center justify-center overflow-hidden rounded-lg bg-sidebar-primary text-sidebar-primary-foreground"
                        >
                            <!-- Use avatar (URL) instead of React Element logo -->
                            <img
                                v-if="selectedWorkspace?.avatar"
                                :src="selectedWorkspace!.avatar"
                                :alt="selectedWorkspace!.name"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="text-xs font-medium">
                                {{ selectedWorkspace?.name?.[0] ?? 'W' }}
                            </div>
                        </div>

                        <div
                            class="grid flex-1 text-left text-sm leading-tight"
                        >
                            <span class="truncate font-medium">
                                {{
                                    selectedWorkspace?.name ??
                                    'Select workspace'
                                }}
                            </span>
                            <!-- Optional second line like 'plan' in original – omitted since Workspace has no plan -->
                            <span class="truncate text-xs opacity-70">
                                {{ selectedWorkspace?.domain ?? '' }}
                            </span>
                        </div>

                        <ChevronsUpDown class="ml-auto" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>

                <!-- CHANGE: Tailwind arbitrary var class corrected -->
                <DropdownMenuContent
                    class="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                    align="start"
                    :side="isMobile ? 'bottom' : 'right'"
                    :side-offset="4"
                >
                    <DropdownMenuLabel class="text-xs text-muted-foreground">
                        Workspaces
                    </DropdownMenuLabel>

                    <DropdownMenuItem
                        v-for="(workspace, index) in workspaces"
                        :key="workspace.domain"
                        class="gap-2 p-2"
                        @click="selectWorkspace(workspace)"
                    >
                        <div
                            class="flex size-6 items-center justify-center overflow-hidden rounded-md border"
                        >
                            <img
                                v-if="workspace.avatar"
                                :src="workspace.avatar"
                                :alt="workspace.name"
                                class="h-full w-full object-cover"
                            />
                            <span v-else class="text-[10px] font-medium">
                                {{ workspace.name[0] }}
                            </span>
                        </div>
                        <span class="truncate">{{ workspace.name }}</span>
                        <DropdownMenuShortcut
                            >⌘{{ index + 1 }}
                        </DropdownMenuShortcut>
                    </DropdownMenuItem>

                    <DropdownMenuSeparator />

                    <DropdownMenuItem class="gap-2 p-2" @click="addWorkspace">
                        <div
                            class="flex size-6 items-center justify-center rounded-md border bg-transparent"
                        >
                            <Plus class="size-4" />
                        </div>
                        <div class="font-medium text-muted-foreground">
                            Add workspace
                        </div>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
