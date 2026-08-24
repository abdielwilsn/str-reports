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
        Schema::create('cryptocurrencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol');
            $table->string('network');
            $table->enum('type', ['native', 'token']);
            $table->string('contract_address')->nullable();
            $table->integer('decimals')->default(18);
            $table->integer('required_confirmations')->default(12);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['network', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cryptocurrencies');
    }
};
