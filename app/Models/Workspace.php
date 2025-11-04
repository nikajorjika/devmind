<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Multitenancy\Models\Tenant;

class Workspace extends Tenant
{
    use HasFactory;

    protected $table = 'workspaces';

    protected $fillable = [
        'name',
        'subdomain',
        'database'
    ];

    /**
     * The users that belong to the workspace.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_workspace');
    }
}
