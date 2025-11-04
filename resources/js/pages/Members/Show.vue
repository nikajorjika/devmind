<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import MembersTable from '@/components/members/MembersTable.vue';
import InviteMemberDialog from '@/components/InviteMemberDialog.vue';
import { Button } from '@/components/ui/button';
import { UserPlus } from 'lucide-vue-next';
import { ref } from 'vue';

type MemberStatus = 'active' | 'pending' | 'inactive';
type MemberRole = 'Owner' | 'Admin' | 'Member' | 'Viewer';

interface Member {
    id: string;
    name: string;
    email: string;
    avatar?: string;
    role: MemberRole;
    status: MemberStatus;
    created_at: string;
}

const props = defineProps<{ members: Member[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/' },
    { title: 'Members', href: '#' },
];

const inviteOpen = ref(false);
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
                        Manage your workspace members and their roles
                    </p>
                </div>

                <InviteMemberDialog v-model:open="inviteOpen">
                    <Button class="gap-2" @click="inviteOpen = true">
                        <UserPlus class="h-4 w-4" />
                        Invite Member
                    </Button>
                </InviteMemberDialog>
            </div>

            <MembersTable :members="props.members" />
        </div>
    </AppLayout>
</template>
