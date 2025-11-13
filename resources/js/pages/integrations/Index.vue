<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Github } from 'lucide-vue-next';

interface Integration {
    id: number;
    provider: string;
    external_name: string;
    avatar_url?: string;
    connected_at: string;
}

interface Provider {
    key: string;
    name: string;
}

interface Props {
    integrations: Record<string, Integration>;
    availableProviders: Provider[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Integrations',
        href: '/integrations',
    },
];

const connectGithub = () => {
    router.post('/integrations/github/redirect');
};

const getIntegration = (providerKey: string): Integration | undefined => {
    return props.integrations[providerKey];
};

const isConnected = (providerKey: string): boolean => {
    return !!getIntegration(providerKey);
};
</script>

<template>
    <Head title="Integrations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold tracking-tight">Integrations</h1>
                <p class="text-muted-foreground">
                    Connect your DevMind organization to external services.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- GitHub Integration Card -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                                <Github class="h-6 w-6" />
                            </div>
                            <div class="flex-1">
                                <CardTitle class="flex items-center gap-2">
                                    GitHub
                                    <Badge
                                        v-if="isConnected('github')"
                                        variant="default"
                                        class="ml-auto"
                                    >
                                        Connected
                                    </Badge>
                                </CardTitle>
                                <CardDescription v-if="!isConnected('github')">
                                    Connect your GitHub organization
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <template v-if="!isConnected('github')">
                            <p class="mb-4 text-sm text-muted-foreground">
                                Connect your DevMind organization to a GitHub organization via the DevMind GitHub App.
                            </p>
                            <Button @click="connectGithub" class="w-full">
                                <Github class="mr-2 h-4 w-4" />
                                Connect GitHub
                            </Button>
                        </template>
                        <template v-else>
                            <div class="flex items-center gap-3 mb-4">
                                <Avatar class="h-10 w-10">
                                    <AvatarImage
                                        :src="getIntegration('github')?.avatar_url"
                                        :alt="getIntegration('github')?.external_name"
                                    />
                                    <AvatarFallback>
                                        {{ getIntegration('github')?.external_name?.substring(0, 2).toUpperCase() }}
                                    </AvatarFallback>
                                </Avatar>
                                <div class="flex-1">
                                    <p class="font-medium">{{ getIntegration('github')?.external_name }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        Connected {{ new Date(getIntegration('github')!.connected_at).toLocaleDateString() }}
                                    </p>
                                </div>
                            </div>
                            <!-- TODO: Add disconnect/re-authorize functionality -->
                        </template>
                    </CardContent>
                </Card>

                <!-- Placeholder for future integrations -->
                <Card class="opacity-50">
                    <CardHeader>
                        <CardTitle>GitLab</CardTitle>
                        <CardDescription>Coming soon</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            GitLab integration will be available soon.
                        </p>
                    </CardContent>
                </Card>

                <Card class="opacity-50">
                    <CardHeader>
                        <CardTitle>Bitbucket</CardTitle>
                        <CardDescription>Coming soon</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            Bitbucket integration will be available soon.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
