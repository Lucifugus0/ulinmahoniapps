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
        Schema::create('m_properties', function (Blueprint $table) {
            $table->integer('idrec')->primary();
            $table->string('slug', 200)->nullable();
            $table->string('tags', 100)->nullable();
            $table->string('name', 200)->nullable();
            $table->string('initial', 10)->nullable();
            $table->text('description')->nullable();
            $table->integer('level_count')->nullable();
            $table->string('province', 200)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('subdistrict', 100)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->string('address', 250)->nullable();
            $table->string('location', 500)->nullable();
            $table->string('latitude', 500)->nullable();
            $table->string('longitude', 500)->nullable();                   
            $table->longText('price')->nullable();
            $table->decimal('price_original_daily', 18, 4)->nullable();
            $table->decimal('price_discounted_daily', 18, 4)->nullable();
            $table->decimal('price_original_monthly', 18, 4)->nullable();
            $table->decimal('price_discounted_monthly', 18, 4)->nullable();
            // $table->longText('features')->nullable();
            // $table->longText('attributes')->nullable();            
            

            $table->longText('general')->nullable();
            $table->longText('security')->nullable();
            $table->longText('amenities')->nullable();
                        
            $table->integer('status')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_properties');
    }
};
