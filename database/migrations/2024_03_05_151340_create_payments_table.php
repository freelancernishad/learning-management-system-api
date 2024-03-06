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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignUuid('course_id')->constrained()->onDelete('cascade');
            $table->string('trxid')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('vat', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('payment_wallet')->nullable();
            $table->string('method')->nullable();
            $table->string('mer_trxid')->nullable();
            $table->timestamp('date')->nullable();
            $table->string('status')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('ipn')->nullable();
            $table->string('payment_url')->nullable();
            $table->string('paymentID')->nullable();
            $table->text('id_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->string('app_key')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
