import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
    workspaces: Workspace[];
    defaultWorkspace: Workspace;
}

export interface Workspace {
    id: number;
    name: string;
    avatar: string;
    domain: string;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface MemberRole {
    id: number;
    name: string;
    guard_name: string;
    description: string | null;
}

export interface MemberMembership {
    workspace_id: number;
    user_id: number;
}

export interface Member {
    id: number;
    name: string;
    email: string;
    status: string;
    email_verified: boolean;
    created_at: string; // ISO8601
    updated_at: string | null; // ISO8601

    workspace_id: number | null;
    membership: MemberMembership;

    role: string; // primary role in current workspace
    role_id: number;
    is_owner: boolean;

    roles: MemberRole[];

    two_factor_enabled: boolean;
}

export interface MembersResource {
    data: Member[];
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    currentWorkspace: Workspace;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
