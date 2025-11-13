<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('version_control_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->string('provider'); // github, gitlab, bitbucket
            $table->string('external_id');
            $table->string('external_name');
            $table->string('installation_id')->nullable(); // For GitHub App installation
            $table->string('avatar_url')->nullable();
            $table->json('meta')->nullable(); // Provider-specific extra data
            $table->timestamp('connected_at');
            $table->timestamp('disconnected_at')->nullable();
            $table->timestamps();

            // Index for queries
            $table->index(['workspace_id', 'provider']);
        });

        // Add a partial unique index for active integrations (where disconnected_at IS NULL)
        // This ensures one active integration per provider per workspace
        // SQLite and PostgreSQL support this; MySQL would need a different approach
        DB::statement('CREATE UNIQUE INDEX workspace_provider_active_unique 
            ON version_control_integrations (workspace_id, provider) 
            WHERE disconnected_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS workspace_provider_active_unique');
        Schema::dropIfExists('version_control_integrations');
    }
};
