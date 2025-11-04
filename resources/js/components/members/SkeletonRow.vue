<template>
    <TableRow v-for="i in 5" :key="i">
        <!-- Member -->
        <TableCell>
            <div class="flex items-center gap-3">
                <Skeleton class="h-8 w-8 rounded-full" />
                <Skeleton class="h-4 w-[120px] rounded" />
            </div>
        </TableCell>

        <!-- Email -->
        <TableCell>
            <Skeleton class="h-4 w-[150px] rounded" />
        </TableCell>

        <!-- Role -->
        <TableCell>
            <Skeleton class="h-4 w-[80px] rounded-full" />
        </TableCell>

        <!-- Status -->
        <TableCell>
            <Skeleton class="h-4 w-[80px] rounded-full" />
        </TableCell>

        <!-- Joined -->
        <TableCell>
            <Skeleton class="h-4 w-[90px] rounded" />
        </TableCell>

        <!-- Actions -->
        <TableCell>
            <Skeleton class="mx-auto h-4 w-4 rounded-full" />
        </TableCell>
    </TableRow>
</template>

<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton/index.js';
import { TableCell, TableRow } from '@/components/ui/table/index.js';
</script>
