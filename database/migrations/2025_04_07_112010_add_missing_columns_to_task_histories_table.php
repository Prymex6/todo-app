<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('changed_by')->after('task_id');
            $table->foreign('changed_by')->references('id')->on('users');
            $table->string('changed_field')->nullable()->after('event_type');
            $table->text('change_comment')->nullable()->after('event_type');
        });
    }

    public function down()
    {
        Schema::table('task_histories', function (Blueprint $table) {
            $table->dropForeign(['changed_by']);
            $table->dropColumn('changed_by');
            $table->dropColumn('changed_field');
            $table->dropColumn('change_comment');
        });
    }
};
