<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_shares', function (Blueprint $table) {
            $table->timestamp('first_accessed_at')->nullable()->after('use_count');
            $table->timestamp('last_accessed_at')->nullable()->after('first_accessed_at');
        });
    }

    public function down()
    {
        Schema::table('task_shares', function (Blueprint $table) {
            $table->dropColumn(['first_accessed_at', 'last_accessed_at']);
        });
    }
};