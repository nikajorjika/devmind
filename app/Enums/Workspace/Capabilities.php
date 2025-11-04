<?php

namespace App\Enums\Workspace;

enum Capabilities: string
{
    case MEMBER_SHOW = 'member.show';
    case MEMBER_INVITE = 'member.invite';
    case MEMBER_REMOVE = 'member.remove';
    case MEMBER_CHANGE_ROLE = 'member.change_role';
    case WORKSPACE_UPDATE = 'workspace.update';
    case WORKSPACE_DELETE = 'workspace.delete';
}
