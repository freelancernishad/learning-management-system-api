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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('instructor')->nullable()->after('course_category_id');
            $table->unsignedDecimal('rating', 3, 2)->default(0)->after('instructor');
            $table->unsignedDecimal('price', 8, 2)->nullable()->after('rating');
            $table->unsignedDecimal('previous_price', 8, 2)->nullable()->after('price');
            $table->unsignedDecimal('discount', 5, 2)->nullable()->after('previous_price');
            $table->text('about_video')->nullable()->after('discount');
            $table->text('targeted_audience')->nullable()->after('about_video');
            $table->text('what_you_learn')->nullable()->after('targeted_audience');
            $table->text('descriptions')->nullable()->after('what_you_learn');
            $table->text('demo_certificate')->nullable()->after('descriptions');
            $table->text('requirements')->nullable()->after('demo_certificate');
            $table->text('features')->nullable()->after('requirements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'instructor',
                'rating',
                'price',
                'previous_price',
                'discount',
                'about_video',
                'targeted_audience',
                'what_you_learn',
                'descriptions',
                'demo_certificate',
                'requirements',
                'features',
    
            ]);
        });
    }
};
