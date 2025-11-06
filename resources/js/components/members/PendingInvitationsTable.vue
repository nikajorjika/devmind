<!-- resources/js/components/members/PendingInvitationsTable.vue -->
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Invitation } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    invitations?: { data?: Invitation[] } | Invitation[];
}>();

const list = computed<Invitation[]>(() => {
    if (Array.isArray(props.invitations)) return props.invitations;
    return props.invitations?.data ?? [];
});

const emits = defineEmits<{
    (e: 'resend', id: Invitation['id']): void;
    (e: 'revoke', id: Invitation['id']): void;
}>();

const dateFmt = new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
});

function resend(id: Invitation['id']) {
    emits('resend', id);
}

function revoke(id: Invitation['id']) {
    emits('revoke', id);
}
</script>

<template>
    <div class="rounded-xl border">
        <div class="flex items-center justify-between border-b p-4">
            <div>
                <h2 class="text-lg font-semibold">Pending Invitations</h2>
                <p class="text-sm text-muted-foreground">
                    Invitations that haven’t been accepted yet.
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
                        <TableHead class="w-[160px] text-right"
                            >Actions
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-if="!list.length">
                        <TableCell
                            colspan="6"
                            class="text-center text-sm text-muted-foreground"
                        >
                            No pending invitations.
                        </TableCell>
                    </TableRow>

                    <TableRow v-for="inv in list" :key="inv.id">
                        <TableCell class="font-medium"
                            >{{ inv.email }}
                        </TableCell>
                        <TableCell>{{ inv.role_name }}</TableCell>
                        <TableCell>{{ inv.inviter?.name ?? '—' }}</TableCell>
                        <TableCell
                            >{{
                                inv.created_at
                                    ? dateFmt.format(new Date(inv.created_at))
                                    : '—'
                            }}
                        </TableCell>
                        <TableCell>
                            <span v-if="inv.expires_at">{{
                                dateFmt.format(new Date(inv.expires_at))
                            }}</span>
                            <span v-else>—</span>
                        </TableCell>
                        <TableCell class="text-right">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="resend(inv.id)"
                                    >Resend
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    @click="revoke(inv.id)"
                                    >Revoke
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
