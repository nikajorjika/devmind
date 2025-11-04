<script setup lang="ts">
import { ref } from "vue"
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Loader2, Mail } from "lucide-vue-next"

type Role = "Owner" | "Admin" | "Member" | "Viewer"

const { open } = defineProps<{ open: boolean }>()
const emit = defineEmits<{ (e: "update:open", value: boolean): void }>()
defineSlots<{ default?: () => unknown }>()

const isLoading = ref(false)
const email = ref("")
const role = ref<Role>("Member")

function onOpenChange(val: boolean) {
    emit("update:open", val)
}

async function handleSubmit(e: Event) {
    e.preventDefault()
    if (!email.value.trim()) return

    isLoading.value = true
    try {
        // Simulate API call
        await new Promise((resolve) => setTimeout(resolve, 800))
        // Replace with real API call
        // await inviteMember({ email: email.value, role: role.value })

        console.log("Inviting:", { email: email.value, role: role.value })

        // Reset form and close
        email.value = ""
        role.value = "Member"
        emit("update:open", false)
        // TODO: trigger success toast
    } catch (err) {
        console.error("Error inviting member:", err)
        // TODO: trigger error toast
    } finally {
        isLoading.value = false
    }
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
                    <Label for="invite-email" class="font-medium">Email Address</Label>
                    <div class="relative">
                        <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground pointer-events-none" />
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
                            <SelectItem value="Owner">Owner</SelectItem>
                            <SelectItem value="Admin">Admin</SelectItem>
                            <SelectItem value="Member">Member</SelectItem>
                            <SelectItem value="Viewer">Viewer</SelectItem>
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
                    <Button type="submit" :disabled="isLoading || !email.trim()">
                        <template v-if="isLoading">
                            <Loader2 class="mr-2 h-4 w-4 animate-spin" />
                            Sending...
                        </template>
                        <template v-else>
                            Send Invite
                        </template>
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
