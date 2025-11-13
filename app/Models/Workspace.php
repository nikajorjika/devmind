<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Tenant;

class Workspace extends Tenant
{
    use HasFactory;

    protected $table = 'workspaces';

    protected $fillable = [
        'name',
        'subdomain',
        'database',
    ];

    /**
     * The users that belong to the workspace.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memberships')
            ->using(Membership::class)
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Get the invitations for the workspace.
     *
     * @return HasMany<Invitation, $this>
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Get the current workspace instance.
     */
    public static function current(): ?self
    {
        return self::current();
    }
}
