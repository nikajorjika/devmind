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

export interface Invitation {
    id: number;
    workspace_id: number;
    inviter_id: number;

    email: string;
    role_name: string;

    token: string; // ULID string
    expires_at: string | null; // ISO timestamp
    accepted_at: string | null;
    accepted_by: number | null;
    revoked_at: string | null;

    status: 'pending' | 'accepted' | 'revoked' | string; // fallback for future statuses
    meta: Record<string, any> | null;

    created_at: string;
    updated_at: string;

    inviter ? : {
        id: number
        name: string
        email: string
    };
    accepted_user ? : {
        id: number
        name: string
        email: string
    };
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
