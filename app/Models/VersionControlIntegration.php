<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VersionControlIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'provider',
        'external_id',
        'external_name',
        'installation_id',
        'avatar_url',
        'meta',
        'connected_at',
        'disconnected_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'connected_at' => 'datetime',
        'disconnected_at' => 'datetime',
    ];

    /**
     * Get the workspace that owns the integration.
     *
     * @return BelongsTo<Workspace, $this>
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Check if the integration is currently active (connected).
     */
    public function isActive(): bool
    {
        return is_null($this->disconnected_at);
    }

    /**
     * Scope a query to only include active integrations.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('disconnected_at');
    }

    /**
     * Scope a query to filter by provider.
     */
    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }
}
