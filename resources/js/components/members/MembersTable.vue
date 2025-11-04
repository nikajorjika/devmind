<script setup lang="ts">
import SkeletonRow from '@/components/members/SkeletonRow.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import {
    Table,
    TableBody,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { MembersResource } from '@/types';
import { Deferred } from '@inertiajs/vue3';
import MemberRow from './MemberRow.vue';

const props = defineProps<{ members?: MembersResource }>();
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="text-xl">Team Members</CardTitle>
            <CardDescription>
                <Deferred data="members">
                    <template #fallback>
                        <Skeleton class="h-5 w-[160px] rounded-full" />
                    </template>
                    {{ props.members?.data.length }} members in your workspace
                </Deferred>
            </CardDescription>
        </CardHeader>

        <CardContent>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Member</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Joined</TableHead>
                        <TableHead class="w-12">Actions</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <Deferred data="members">
                        <template #fallback>
                            <SkeletonRow />
                        </template>

                        <MemberRow
                            v-for="member in props.members?.data"
                            :key="member.id"
                            :member="member"
                        />
                    </Deferred>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>
