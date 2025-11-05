<script setup lang="ts">
import InputError from '@/components/InputError.vue';
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
import { inviteMember } from '@/routes'; // Wayfinder helper
import { MemberRole } from '@/types';
import { Form, usePage } from '@inertiajs/vue3';
import { Loader2, Mail } from 'lucide-vue-next';
import { ref } from 'vue';

const { open } = defineProps<{ open: boolean }>();
const emit = defineEmits<{ (e: 'update:open', value: boolean): void }>();
defineSlots<{ default?: () => unknown }>();

const roles = (usePage().props.roles as MemberRole[]) ?? [];
const role = ref<string | null>(null); // local for the Select

function onOpenChange(val: boolean) {
    emit('update:open', val);
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

            <!-- Inertia Form: Wayfinder-provided form config -->
            <Form
                v-bind="inviteMember.form()"
                :reset-on-success="['email', 'role']"
                @success="
                    () => {
                        role = null;
                        onOpenChange(false);
                    }
                "
                v-slot="{ errors, processing }"
                class="space-y-4"
            >
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
                            name="email"
                            placeholder="member@example.com"
                            :disabled="processing"
                            required
                            class="pl-10"
                            autocomplete="email"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <!-- Role Field -->
                <div class="space-y-2">
                    <Label for="invite-role" class="font-medium">Role</Label>
                    <Input type="hidden" name="role" :value="role ?? ''" />
                    <Select v-model="role" :disabled="processing">
                        <SelectTrigger id="invite-role">
                            <SelectValue placeholder="Select a role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="r in roles"
                                :key="r.name"
                                :value="r.name"
                            >
                                {{ r.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <p class="text-xs text-muted-foreground">
                        Choose the role permissions for this member
                    </p>
                    <InputError :message="errors.role" />
                </div>

                <!-- Footer -->
                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="processing"
                        @click="onOpenChange(false)"
                    >
                        Cancel
                    </Button>

                    <Button type="submit" :disabled="processing || !role">
                        <template v-if="processing">
                            <Loader2 class="mr-2 h-4 w-4 animate-spin" />
                            Sending...
                        </template>
                        <template v-else>Send Invite</template>
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
