<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id', 'inviter_id', 'email', 'role_name',
        'token', 'expires_at', 'accepted_at', 'accepted_by',
        'revoked_at', 'status', 'meta',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'revoked_at' => 'datetime',
        'meta' => 'array',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function invitee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->whereNull('revoked_at')
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    #[Scope]
    protected function inCurrentWorkspace(Builder $query): Builder
    {
        return $query->where('workspace_id', auth()->user()->current_workspace_id);
    }

    #[Scope]
    protected function for(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && !$this->isExpired() && !$this->revoked_at;
    }

    public function isRevoked(): bool
    {
        return (bool) $this->revoked_at;
    }

    public function isAccepted(): bool
    {
        return (bool) $this->accepted_at;
    }
}
