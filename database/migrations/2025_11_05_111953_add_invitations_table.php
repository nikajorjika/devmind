<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inviter_id')->constrained('users')->cascadeOnDelete();

            $table->string('email')->index();
            $table->string('role_name');

            $table->ulid('token')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('accepted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revoked_at')->nullable();

            $table->string('status')->default('pending');
            $table->json('meta')->nullable();

            $table->unique(['workspace_id', 'email'], 'uniq_pending_invite')
                ->where(fn($q) => $q->where('status', 'pending'));

            $table->timestamps();
        });
    }
};
