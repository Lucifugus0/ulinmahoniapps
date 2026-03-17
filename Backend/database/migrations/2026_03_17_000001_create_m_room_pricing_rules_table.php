<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * <!-- Multi-Tier Pricing: Create m_room_pricing_rules table -->
 * <!-- Stores admin-configured pricing rules per room (weekday, weekend, holiday, high_season, low_season) -->
 * <!-- Each rule has a type, price, and optional date range. The RoomPriceGeneratorService resolves -->
 * <!-- these rules into per-date prices in m_room_prices using priority: high_season > low_season > holiday > weekend > weekday -->
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_room_pricing_rules', function (Blueprint $table) {
            $table->increments('idrec');
            $table->integer('room_id');                                  // FK to m_rooms.idrec
            $table->string('rule_type', 20);                             // 'weekday','weekend','holiday','high_season','low_season'
            $table->decimal('price', 18, 4);                             // Price for this rule
            $table->date('date_start')->nullable();                      // NULL for weekday/weekend; start date for holiday/season
            $table->date('date_end')->nullable();                        // NULL for weekday/weekend; end date for holiday/season
            $table->string('label', 100)->nullable();                    // Human-readable name, e.g. "Eid al-Fitr"
            $table->tinyInteger('status')->default(1);                   // 1=active, 0=inactive
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            $table->index(['room_id', 'rule_type'], 'idx_room_rule_type');
            $table->index(['room_id', 'date_start', 'date_end'], 'idx_room_dates');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_room_pricing_rules');
    }
};
