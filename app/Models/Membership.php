<?php

namespace App\Models;

use App\Enums\Member\MemberStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Membership extends Pivot
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'status',
    ];

    public function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'status' => MemberStatus::class,
        ];
    }

    /**
     * Get the user that owns the membership.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workspace that owns the membership.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Workspace, $this>
     */
    public function workspace(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
