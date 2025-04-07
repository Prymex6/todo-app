<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_shares', function (Blueprint $table) {
            $table->integer('max_uses')->nullable()->after('expires_at');
            $table->boolean('allow_editing')->default(false)->after('max_uses');
            $table->string('shared_with_email')->nullable()->after('allow_editing');
            $table->foreignId('shared_by')->constrained('users')->after('task_id');
            
            $table->integer('use_count')->default(0)->after('shared_with_email');
        });
    }

    public function down(): void
    {
        Schema::table('task_shares', function (Blueprint $table) {
            $table->dropColumn(['max_uses', 'allow_editing', 'shared_with_email', 'shared_by', 'use_count']);
        });
    }
};
