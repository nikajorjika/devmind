<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/workspace';
import { Form } from '@inertiajs/vue3';
import { Loader2, Upload } from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref } from 'vue';

const props = withDefaults(defineProps<{ baseHost?: string }>(), {
    baseHost: 'devmind.com',
});

function slugify(input: string): string {
    return input
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9-]/g, '-')
        .replace(/-{2,}/g, '-')
        .replace(/^-+|-+$/g, '');
}

function getInitials(name: string): string {
    return (
        name
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map((w) => w[0]!.toUpperCase())
            .join('') || 'WS'
    );
}

const workspaceName = ref('');
const workspaceDomain = ref(''); // slug only
const avatarFile = ref<File | null>(null);
const avatarPreviewUrl = ref<string>('');
const fileInputId = 'workspace-avatar-input';
const domainTouched = ref(false);

const previewUrl = computed(() =>
    workspaceDomain.value
        ? `https://${workspaceDomain.value}.${props.baseHost}`
        : '',
);

function onDomainInput(val: string) {
    domainTouched.value = val.length > 0;
    workspaceDomain.value = slugify(val);
}

function onNameInput(val: string) {
    workspaceName.value = val;
    if (!domainTouched.value) {
        workspaceDomain.value = slugify(val);
    }
}

function setAvatarFile(file: File | null) {
    if (avatarPreviewUrl.value) {
        URL.revokeObjectURL(avatarPreviewUrl.value);
        avatarPreviewUrl.value = '';
    }
    avatarFile.value = file;
    if (file) {
        avatarPreviewUrl.value = URL.createObjectURL(file);
    }
}

function handleAvatarChange(e: Event) {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;
    if (!file) return setAvatarFile(null);
    if (!file.type.startsWith('image/')) return setAvatarFile(null);
    if (file.size > 5 * 1024 * 1024) return setAvatarFile(null);
    setAvatarFile(file);
}

onBeforeUnmount(() => {
    if (avatarPreviewUrl.value) URL.revokeObjectURL(avatarPreviewUrl.value);
});

function resetLocalState() {
    workspaceName.value = '';
    workspaceDomain.value = '';
    domainTouched.value = false;
    setAvatarFile(null);
}

const canSubmit = computed(
    () =>
        workspaceName.value.trim().length >= 3 &&
        workspaceDomain.value.trim().length >= 3,
);
</script>

<template>
    <Card class="border border-border bg-card p-6">
        <!-- Matches your login pattern: Form + store.form() + slot { errors, processing } -->
        <Form
            v-bind="store.form()"
            enctype="multipart/form-data"
            :reset-on-success="['name', 'subdomain', 'avatar']"
            @success="resetLocalState"
            v-slot="{ errors, processing }"
            class="space-y-6"
        >
            <!-- Avatar Section -->
            <div class="flex flex-col items-center">
                <div class="mb-4">
                    <Avatar class="h-24 w-24">
                        <AvatarImage
                            v-if="avatarPreviewUrl"
                            :src="avatarPreviewUrl"
                            alt="Workspace avatar"
                        />
                        <AvatarFallback class="text-lg font-semibold">
                            {{ getInitials(workspaceName) }}
                        </AvatarFallback>
                    </Avatar>
                </div>

                <input
                    :id="fileInputId"
                    type="file"
                    name="avatar"
                    accept="image/*"
                    class="hidden"
                    aria-label="Upload workspace avatar"
                    @change="handleAvatarChange"
                />
                <Label
                    :for="fileInputId"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-2 text-sm"
                >
                    <Upload class="h-4 w-4" />
                    Upload Logo
                </Label>

                <p class="mt-2 text-xs text-muted-foreground">
                    Optional • PNG, JPG up to 5 MB
                </p>
                <!-- show avatar error if backend returns one -->
                <p v-if="errors.avatar" class="mt-2 text-xs text-destructive">
                    {{ errors.avatar }}
                </p>
            </div>

            <!-- Workspace Name -->
            <div class="space-y-2">
                <Label for="workspace-name" class="font-medium"
                    >Workspace Name</Label
                >
                <Input
                    id="workspace-name"
                    name="name"
                    placeholder="e.g., Acme Inc."
                    :disabled="processing"
                    required
                    aria-required="true"
                    class="h-10"
                    :maxlength="60"
                    v-model.trim="workspaceName"
                    @input="
                        onNameInput(($event.target as HTMLInputElement).value)
                    "
                    autocomplete="organization"
                />
                <p class="text-xs text-muted-foreground">
                    This is the display name for your workspace
                </p>
                <p v-if="errors.name" class="text-xs text-destructive">
                    {{ errors.name }}
                </p>
            </div>

            <!-- Workspace Domain (slug-only input with https:// and .host adornments) -->
            <div class="space-y-2">
                <Label for="workspace-subdomain" class="font-medium"
                    >Workspace Domain</Label
                >
                <div class="flex items-stretch">
                    <span
                        class="flex items-center rounded-l-md border border-r-0 border-input bg-muted px-3 text-sm text-muted-foreground"
                    >
                        https://
                    </span>

                    <Input
                        id="workspace-subdomain"
                        name="subdomain"
                        placeholder="workspace-slug"
                        :disabled="processing"
                        required
                        aria-required="true"
                        class="h-10 rounded-none border-x-0"
                        :maxlength="30"
                        v-model.trim="workspaceDomain"
                        @input="
                            onDomainInput(
                                ($event.target as HTMLInputElement).value,
                            )
                        "
                        autocomplete="off"
                        inputmode="text"
                        pattern="^[a-z0-9]+(?:-[a-z0-9]+)*$"
                    />

                    <span
                        class="flex items-center rounded-r-md border border-l-0 border-input bg-muted px-3 text-sm text-muted-foreground"
                    >
                        .{{ props.baseHost }}
                    </span>
                </div>

                <p v-if="previewUrl" class="text-xs text-muted-foreground">
                    Preview: <span class="font-mono">{{ previewUrl }}</span>
                </p>
                <p class="text-xs text-muted-foreground">
                    Choose a unique URL slug for your workspace
                </p>
                <p v-if="errors.subdomain" class="text-xs text-destructive">
                    {{ errors.subdomain }}
                </p>
            </div>

            <Button
                type="submit"
                class="h-10 w-full font-medium"
                :disabled="processing || !canSubmit"
            >
                <template v-if="processing">
                    <Loader2 class="mr-2 h-4 w-4 animate-spin" />
                    Creating workspace…
                </template>
                <template v-else>Create Workspace</template>
            </Button>
        </Form>
    </Card>
</template>
