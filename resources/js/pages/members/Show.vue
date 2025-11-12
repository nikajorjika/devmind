<script setup lang="ts">
import InviteMemberDialog from '@/components/members/InviteMemberDialog.vue';
import MembersTable from '@/components/members/MembersTable.vue';
import PendingInvitationsTable from '@/components/members/PendingInvitationsTable.vue';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    type BreadcrumbItem,
    InvitationResource,
    MembersResource,
} from '@/types';
import { Head } from '@inertiajs/vue3';
import { UserPlus } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    members?: MembersResource;
    invitations?: InvitationResource; // pending invitations (paginated or array)
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/' },
    { title: 'Members', href: '#' },
];

const inviteOpen = ref(false);
const pendingCount = computed(() => props.invitations?.data?.length ?? 0);
const memberCount = computed(() => props.members?.data?.length ?? 0);
</script>

<template>
    <Head title="Members" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <!-- Header -->
            <div class="flex items-center justify-between gap-4">
                <div class="flex flex-col gap-2">
                    <h1 class="text-3xl font-bold tracking-tight">Members</h1>
                    <p class="text-muted-foreground">
                        Manage your workspace members, invitations, and roles
                    </p>
                </div>

                <InviteMemberDialog v-model:open="inviteOpen">
                    <Button class="gap-2" @click="inviteOpen = true">
                        <UserPlus class="h-4 w-4" />
                        Invite Member
                    </Button>
                </InviteMemberDialog>
            </div>

            <!-- Tabs -->
            <Tabs default-value="members" class="w-full">
                <TabsList class="grid w-full max-w-md grid-cols-2">
                    <TabsTrigger value="members"
                        >Members
                        <span
                            v-if="memberCount"
                            class="ml-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary/10 px-1.5 text-xs font-medium text-primary"
                        >
                            {{ memberCount }}
                        </span>
                    </TabsTrigger>
                    <TabsTrigger value="invitations" class="relative">
                        Pending Invitations
                        <span
                            v-if="pendingCount"
                            class="ml-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary/10 px-1.5 text-xs font-medium text-primary"
                        >
                            {{ pendingCount }}
                        </span>
                    </TabsTrigger>
                </TabsList>

                <!-- Members Tab -->
                <TabsContent value="members" class="mt-4">
                    <MembersTable :members="props.members" />
                </TabsContent>

                <!-- Invitations Tab -->
                <TabsContent value="invitations" class="mt-4">
                    <PendingInvitationsTable :invitations="props.invitations" />
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
