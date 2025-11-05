<script setup lang="ts">
import CreateWorkspaceForm from '@/components/workspace/CreateWorkspaceForm.vue'
</script>

<template>
    <main class="bg-background min-h-screen px-4 py-12 flex items-center justify-center">
        <div class="w-full max-w-md">
            <!-- Header Section -->
            <div class="mb-8 text-center">
                <h1 class="mb-2 text-3xl font-bold text-foreground">Create Your Workspace</h1>
                <p class="text-muted-foreground">Set up your first workspace to get started</p>
            </div>

            <!-- Form Component -->
            <CreateWorkspaceForm />

            <!-- Footer Note -->
            <p class="mt-8 text-center text-sm text-muted-foreground">
                You can always change these settings later
            </p>
        </div>
    </main>
</template>
