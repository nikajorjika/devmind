<!-- resources/js/components/members/PendingInvitationsTable.vue -->
<script setup lang="ts">
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { computed } from 'vue';
import { Invitation } from '@/types';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const props = defineProps<{
    invitations?: { data?: Invitation[] } | Invitation[];
}>();

const list = computed<Invitation[]>(() => {
    // Support both array and paginator-like shape
    if (Array.isArray(props.invitations)) return props.invitations;
    return props.invitations?.data ?? [];
});

function resend(invitation: Invitation) {
    router.post(
        `/members/invitations/${invitation.id}/resend`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Invitation Resent', {
                    description: `Invitation email sent to ${invitation.email}`,
                });
            },
            onError: (errors) => {
                toast.error('Failed to Resend', {
                    description: errors.invitation || 'An error occurred while resending the invitation',
                });
            },
        }
    );
}

function revoke(invitation: Invitation) {
    if (!confirm(`Are you sure you want to revoke the invitation for ${invitation.email}?`)) {
        return;
    }
    
    router.delete(
        `/members/invitations/${invitation.id}/revoke`,
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Invitation Revoked', {
                    description: `Invitation for ${invitation.email} has been revoked`,
                });
            },
            onError: (errors) => {
                toast.error('Failed to Revoke', {
                    description: errors.invitation || 'An error occurred while revoking the invitation',
                });
            },
        }
    );
}
</script>

<template>
    <div class="rounded-xl border">
        <div class="flex items-center justify-between border-b p-4">
            <div>
                <h2 class="text-lg font-semibold">Pending Invitations</h2>
                <p class="text-sm text-muted-foreground">
                    Invitations that haven't been accepted yet.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Email</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead>Invited By</TableHead>
                        <TableHead>Invited At</TableHead>
                        <TableHead>Expires</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead class="w-[160px] text-right"
                            >Actions</TableHead
                        >
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-if="!list.length">
                        <TableCell
                            colspan="7"
                            class="text-center text-sm text-muted-foreground"
                        >
                            No pending invitations.
                        </TableCell>
                    </TableRow>

                    <TableRow v-for="inv in list" :key="inv.id">
                        <TableCell class="font-medium">{{
                            inv.email
                        }}</TableCell>
                        <TableCell>{{ inv.role_name }}</TableCell>
                        <TableCell>{{ inv.inviter?.name ?? '—' }}</TableCell>
                        <TableCell>{{
                            inv.created_at
                                ? new Date(inv.created_at).toLocaleString()
                                : '—'
                        }}</TableCell>
                        <TableCell>
                            <span v-if="inv.expires_at" :class="{'text-destructive': inv.is_expired}">
                                {{ new Date(inv.expires_at).toLocaleDateString() }}
                            </span>
                            <span v-else>—</span>
                        </TableCell>
                        <TableCell>
                            <span
                                class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                :class="{
                                    'bg-yellow-100 text-yellow-800': inv.is_pending,
                                    'bg-red-100 text-red-800': inv.is_expired,
                                }"
                            >
                                {{ inv.is_expired ? 'Expired' : inv.status }}
                            </span>
                        </TableCell>
                        <TableCell class="text-right">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :disabled="!inv.is_pending"
                                    @click="resend(inv)"
                                    >Resend</Button
                                >
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    :disabled="!inv.is_pending"
                                    @click="revoke(inv)"
                                    >Revoke</Button
                                >
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
