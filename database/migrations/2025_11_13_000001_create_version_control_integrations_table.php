<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

            // Ensure one active integration per provider per workspace
            $table->unique(['workspace_id', 'provider', 'disconnected_at'], 'workspace_provider_active_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('version_control_integrations');
    }
};
