<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { TableCell, TableRow } from '@/components/ui/table'
import { MoreVertical } from 'lucide-vue-next'

type MemberStatus = 'active' | 'pending' | 'inactive'
type MemberRole = 'Owner' | 'Admin' | 'Member' | 'Viewer'

interface Member {
    id: string
    name: string
    email: string
    avatar?: string
    role: MemberRole
    status: MemberStatus
    created_at: string
}

const props = defineProps<{ member: Member }>()

function getStatusClasses(status: MemberStatus) {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800 border-green-200'
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 border-yellow-200'
        case 'inactive':
        default:
            return 'bg-gray-100 text-gray-800 border-gray-200'
    }
}

function getRoleVariant(role: MemberRole): 'default' | 'secondary' | 'outline' {
    switch (role) {
        case 'Owner':
            return 'default'
        case 'Admin':
            return 'secondary'
        case 'Member':
        case 'Viewer':
        default:
            return 'outline'
    }
}

function initials(name: string) {
    return name
        .split(' ')
        .filter(Boolean)
        .map((n) => n[0]?.toUpperCase())
        .join('')
}

const dateFmt = new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
})
</script>

<template>
    <TableRow>
        <!-- Member -->
        <TableCell>
            <div class="flex items-center gap-3">
                <Avatar class="h-8 w-8">
                    <AvatarImage :src="props.member.avatar || '/placeholder.svg'" :alt="props.member.name" />
                    <AvatarFallback>{{ initials(props.member.name) }}</AvatarFallback>
                </Avatar>
                <span class="text-sm font-medium">{{ props.member.name }}</span>
            </div>
        </TableCell>

        <!-- Email -->
        <TableCell>
      <span class="text-sm text-muted-foreground">
        {{ props.member.email }}
      </span>
        </TableCell>

        <!-- Role -->
        <TableCell>
            <Badge :variant="getRoleVariant(props.member.role)" class="font-medium">
                {{ props.member.role }}
            </Badge>
        </TableCell>

        <!-- Status -->
        <TableCell>
            <Badge
                variant="outline"
                :class="`font-medium capitalize ${getStatusClasses(props.member.status)}`"
            >
                {{ props.member.status }}
            </Badge>
        </TableCell>

        <!-- Joined -->
        <TableCell>
      <span class="text-sm text-muted-foreground">
        {{ dateFmt.format(new Date(props.member.created_at)) }}
      </span>
        </TableCell>

        <!-- Actions -->
        <TableCell>
            <button class="rounded p-1 transition-colors hover:bg-muted" aria-label="Actions">
                <MoreVertical class="h-4 w-4 text-muted-foreground" />
            </button>
        </TableCell>
    </TableRow>
</template>
