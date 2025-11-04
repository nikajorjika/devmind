<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { MemberRole } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Loader2, Mail } from 'lucide-vue-next';
import { ref } from 'vue';

const { open } = defineProps<{ open: boolean }>();
const emit = defineEmits<{ (e: 'update:open', value: boolean): void }>();
defineSlots<{ default?: () => unknown }>();

const isLoading = ref(false);
const email = ref('');
const role = ref<string | null>(null);
const roles = usePage().props.roles as MemberRole[];

function onOpenChange(val: boolean) {
    emit('update:open', val);
}

async function handleSubmit(e: Event) {
    e.preventDefault();
    const form = { email: email.value, role: role.value };
    // TODO: Implement the actual invitation logic here
    console.log('Inviting member:', { email: email.value, role: role.value });
}
</script>

<template>
    <Dialog :open="open" @update:open="onOpenChange">
        <!-- Trigger slot -->
        <slot />

        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Invite Member</DialogTitle>
                <DialogDescription>
                    Send an invitation to add a new member to your workspace
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit="handleSubmit">
                <!-- Email Field -->
                <div class="space-y-2">
                    <Label for="invite-email" class="font-medium"
                        >Email Address</Label
                    >
                    <div class="relative">
                        <Mail
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            id="invite-email"
                            type="email"
                            placeholder="member@example.com"
                            v-model="email"
                            :disabled="isLoading"
                            required
                            class="pl-10"
                        />
                    </div>
                </div>

                <!-- Role Field -->
                <div class="space-y-2">
                    <Label for="invite-role" class="font-medium">Role</Label>
                    <Select v-model="role" :disabled="isLoading">
                        <SelectTrigger id="invite-role">
                            <SelectValue placeholder="Select a role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="role in roles"
                                :key="role.name"
                                :value="role.name"
                            >
                                {{ role.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p class="text-xs text-muted-foreground">
                        Choose the role permissions for this member
                    </p>
                </div>

                <!-- Footer -->
                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="isLoading"
                        @click="onOpenChange(false)"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        :disabled="isLoading || !email.trim()"
                    >
                        <template v-if="isLoading">
                            <Loader2 class="mr-2 h-4 w-4 animate-spin" />
                            Sending...
                        </template>
                        <template v-else> Send Invite</template>
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
