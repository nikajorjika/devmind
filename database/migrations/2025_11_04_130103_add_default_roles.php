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
        // Add default roles to the roles table
        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
        });

        \Spatie\Permission\Models\Role::createMa([
            'name' => 'Owner',
            'description' => 'Administrator with full access to all resources.',
        ]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
