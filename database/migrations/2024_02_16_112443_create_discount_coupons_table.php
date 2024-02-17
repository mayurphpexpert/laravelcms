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
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->id();

            //discount coupon code
            $table->string('code');
            
            //human readable discount coupon codename
            $table->string('name')->nullable();

            //description of the coupon - not necessary
            $table->text('description');

            //the max uses this discount coupon has
            $table->integer('max_uses')->nullable();

            //how many times a user can user this coupon
            $table->integer('max_uses_user')->nullable();

            //whether or not the coupon is a percentage or a fixed price.
            $table->enum('type',['percent','fixed'])->default('fixed');

            //the amount to discount based on type
            $table->double('discount_amount', 10, 2);

            //the amount to min based on type
            $table->double('min_amount', 10, 2)->nullable();

            $table->integer('status')->default(1);

            //when the coupon begins
            $table->timestamp('starts_at')->nullable();

            //when the coupon ends
            $table->timestamp('expires_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_coupons');
    }
};
