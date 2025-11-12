<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { approve, reject } from '@/routes/invitation';
import { Invitation } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { AlertCircle, CheckCircle2, Clock, XCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{ invitation: Invitation; token: string }>();

const isLoading = ref(false);

const expiresDate = computed(() => {
    return new Date(props.invitation.expires_at);
});
const isExpired = computed(() => props.invitation.is_expired);

const formatDate = (d: Date) =>
    new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(d);

const handleAccept = async () => {
    router.post(approve({ token: props.token }), {}, { preserveScroll: true });
};

const handleDecline = async () => {
    router.delete(reject(props.token), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="fixed inset-0 z-50 overflow-y-auto bg-white">
            <div class="min-h-dvh bg-gradient-to-b from-background to-muted/20">
                <div class="container mx-auto max-w-md px-4 py-8">
                    <!-- Header -->
                    <div class="mb-8 text-center">
                        <h1 class="mb-2 text-3xl font-bold text-balance">
                            You're invited!
                        </h1>
                        <p class="text-balance text-muted-foreground">
                            {{ invitation.inviter?.name }} invited you to join
                            their workspace
                        </p>
                    </div>

                    <!-- Main Card -->
                    <Card class="mb-6 border-2">
                        <CardContent class="p-8">
                            <!-- Status Alert -->
                            <div
                                v-if="isExpired"
                                class="mb-6 flex items-start gap-3 rounded-lg border border-destructive/20 bg-destructive/10 p-3"
                            >
                                <AlertCircle
                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-destructive"
                                />
                                <div>
                                    <p
                                        class="text-sm font-semibold text-destructive"
                                    >
                                        Invitation expired
                                    </p>
                                    <p class="mt-1 text-xs text-destructive/80">
                                        This invitation is no longer valid.
                                        Please contact your administrator.
                                    </p>
                                </div>
                            </div>

                            <!-- Invitation Details -->
                            <div class="space-y-4">
                                <!-- Inviter Info -->
                                <div
                                    class="flex items-center gap-3 border-b pb-4"
                                >
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10"
                                    >
                                        <span
                                            class="text-sm font-semibold text-primary"
                                        >
                                            {{
                                                invitation.inviter?.name.charAt(
                                                    0,
                                                )
                                            }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">From</p>
                                        <p
                                            class="font-semibold text-foreground"
                                        >
                                            {{ invitation.inviter?.name }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="space-y-2">
                                    <p
                                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                    >
                                        Invited email
                                    </p>
                                    <p class="font-medium text-foreground">
                                        {{ invitation.email }}
                                    </p>
                                </div>

                                <!-- Role -->
                                <div class="space-y-2">
                                    <p
                                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                    >
                                        Role
                                    </p>
                                    <div class="flex items-center gap-2">
                                        <Badge
                                            variant="secondary"
                                            class="px-3 py-1 text-sm"
                                        >
                                            {{ invitation.role_name }}
                                        </Badge>
                                    </div>
                                </div>

                                <!-- Expiration -->
                                <div class="space-y-2 pt-2">
                                    <p
                                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                    >
                                        Expires
                                    </p>
                                    <div
                                        class="flex items-center gap-2 text-sm text-foreground"
                                    >
                                        <Clock
                                            class="h-4 w-4 text-muted-foreground"
                                        />
                                        <span>{{
                                            formatDate(expiresDate)
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <Button
                            @click="handleDecline"
                            :disabled="isLoading || isExpired"
                            variant="outline"
                            class="h-11 flex-1 cursor-pointer bg-transparent"
                        >
                            <XCircle class="mr-2 h-4 w-4" />
                            Decline
                        </Button>

                        <Button
                            @click="handleAccept"
                            :disabled="isLoading || isExpired"
                            class="h-11 flex-1 cursor-pointer"
                        >
                            <CheckCircle2 class="mr-2 h-4 w-4" />
                            {{ isLoading ? 'Accepting...' : 'Accept' }}
                        </Button>
                    </div>

                    <!-- Footer Info -->
                    <p class="mt-6 text-center text-xs text-muted-foreground">
                        By accepting this invitation, you agree to join the
                        workspace with the role of
                        <span class="font-semibold">{{
                            invitation.role_name
                        }}</span
                        >.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
