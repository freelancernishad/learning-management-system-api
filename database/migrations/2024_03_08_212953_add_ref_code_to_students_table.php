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
        Schema::table('students', function (Blueprint $table) {
            $table->string('ref_code')->nullable()->unique();
            $table->unsignedBigInteger('referedby')->nullable();
            $table->foreign('referedby')->references('id')->on('students')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('ref_code');
            $table->dropForeign(['referedby']);
            $table->dropColumn('referedby');
        });
    }
};
