<script setup lang="ts">
import NavFooter from '@/components/nav/NavFooter.vue';
import NavMain from '@/components/nav/NavMain.vue';
import NavUser from '@/components/nav/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
} from '@/components/ui/sidebar';
import WorkspaceSwitcher from '@/components/workspace/WorkspaceSwitcher.vue';
import { dashboard, showMembers } from '@/routes';
import { index as developersIndex } from '@/routes/developers';
import { index as projectIndex } from '@/routes/projects';
import { type NavItem } from '@/types';
import {
    Blocks,
    BookOpen,
    ChartColumnIcon,
    Folder,
    UserCog,
    Users,
} from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: ChartColumnIcon,
    },
    {
        title: 'Projects',
        href: projectIndex(),
        icon: Blocks,
    },
    {
        title: 'Developers',
        href: developersIndex(),
        icon: Users,
    },
    {
        title: 'Members',
        href: showMembers(),
        icon: UserCog,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <WorkspaceSwitcher />
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
