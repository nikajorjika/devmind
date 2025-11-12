<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { invitationResend, invitationRevoke } from '@/routes'; // Wayfinder
import type { Invitation } from '@/types';
import { router } from '@inertiajs/vue3';
import { Loader2, RefreshCw, Trash } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{ invitation: Invitation }>();

const loadingResend = ref(false);
const loadingRevoke = ref(false);

function handleResend() {
    loadingResend.value = true;
    router.post(
        invitationResend({ invitation: props.invitation.id }),
        {},
        {
            preserveScroll: true,
            onError: (errs) => {
                const msg =
                    (errs && (errs.invitation || errs.message)) ??
                    'Unable to resend right now.';
                toast('Resend failed', { description: String(msg) });
            },
            onFinish: () => (loadingResend.value = false),
        },
    );
}

function handleRevoke() {
    loadingRevoke.value = true;
    router.delete(invitationRevoke({ invitation: props.invitation.id }), {
        preserveScroll: true,
        onError: (errs) => {
            const msg =
                (errs && (errs.invitation || errs.message)) ??
                'Unable to revoke right now.';
            toast('Revoke failed', { description: String(msg) });
        },
        onFinish: () => (loadingRevoke.value = false),
    });
}
</script>

<template>
    <div class="flex justify-end gap-2">
        <!-- Resend -->
        <AlertDialog>
            <AlertDialogTrigger as-child>
                <Button variant="outline" size="sm" :disabled="loadingResend">
                    <component
                        :is="loadingResend ? Loader2 : RefreshCw"
                        class="mr-1 h-4 w-4 animate-spin"
                        v-if="loadingResend"
                    />
                    <RefreshCw v-else class="mr-1 h-4 w-4" />
                    Resend
                </Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Resend invitation?</AlertDialogTitle>
                    <AlertDialogDescription>
                        We’ll resend the email to <b>{{ invitation.email }}</b
                        >. This may be rate limited.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction
                        :disabled="loadingResend"
                        @click="handleResend"
                        >Resend
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>

        <!-- Revoke -->
        <AlertDialog>
            <AlertDialogTrigger as-child>
                <Button
                    variant="destructive"
                    size="sm"
                    :disabled="loadingRevoke"
                >
                    <component
                        :is="loadingRevoke ? Loader2 : Trash"
                        class="mr-1 h-4 w-4 animate-spin"
                        v-if="loadingRevoke"
                    />
                    <Trash v-else class="mr-1 h-4 w-4" />
                    Revoke
                </Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Revoke this invitation?</AlertDialogTitle>
                    <AlertDialogDescription>
                        This will immediately invalidate the token. The user
                        won’t be able to accept it.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction
                        :disabled="loadingRevoke"
                        @click="handleRevoke"
                        >Revoke
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
