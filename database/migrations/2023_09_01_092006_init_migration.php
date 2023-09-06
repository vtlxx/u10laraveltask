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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 128);
            $table->string('phone_number', 14);
            $table->string('address', 256);
            $table->string('email', 256);
        });

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('ttn');
            $table->smallInteger('width', false, true);
            $table->smallInteger('height', false, true);
            $table->smallInteger('length', false, true);
            $table->smallInteger('weight', false, true);
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade')
                ->onUpdate('cascade');

//          For multiple delivery services support
//            $table->unsignedBigInteger('delivery_service');
//            $table->foreign('delivery_company')
//                ->references('id')
//                ->on('services')
//                ->onDelete('cascade')
//                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
