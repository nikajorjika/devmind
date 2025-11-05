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
    email_verified: boolean;
    role?: string; // primary role in current workspace
    is_owner: boolean;
    two_factor_enabled: boolean;
}

export interface Invitation {
    id: number;
    email: string;
    role_name: string;
    status: 'pending' | 'accepted' | 'revoked' | 'expired' | string;
    expires_at: string | null; // ISO timestamp
    created_at: string;
    
    inviter?: {
        id: number;
        name: string;
    };
    
    is_expired: boolean;
    is_pending: boolean;
}

export interface MembersResource {
    data: Member[];
}

export interface InvitationResource {
    data: Invitation[];
}

type SimpleFlash = {
    success?: string | null;
    error?: string | null;
    warning?: string | null;
    info?: string | null;
};

// status keys we’ll accept from backend for the advanced API
type ToastStatus = 'success' | 'error' | 'warning' | 'info' | 'default';

export type ToastPayload =
    | {
    status?: ToastStatus;
    title?: string;
    message?: string;
    description?: string;
    // anything vue-sonner accepts (duration, closeButton, action, etc.)
    [key: string]: unknown;
}
    | string;

export interface FlashProps extends SimpleFlash {
    toast?: ToastPayload | ToastPayload[] | null;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    currentWorkspace: Workspace;
    flash?: FlashProps;
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
