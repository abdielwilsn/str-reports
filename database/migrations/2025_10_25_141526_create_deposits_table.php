<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositsTable extends Migration
{
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cryptocurrency_id')->nullable()->constrained()->onDelete('cascade');

            $table->string('transaction_hash')->nullable();
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();

            $table->decimal('expected_amount', 30, 18)->nullable();
            $table->decimal('actual_amount', 30, 18)->nullable();
            $table->decimal('amount', 30, 18)->nullable();

            $table->string('proof_of_payment')->nullable();

            $table->enum('status', ['pending', 'verifying', 'confirmed', 'failed', 'mismatch'])
                ->default('pending');
            $table->integer('confirmations')->default(0);
            $table->text('verification_error')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('user_id');
            $table->index('created_at');

            // Remove unique constraint on tx hash since manual deposits won't have one
            // $table->unique(['cryptocurrency_id', 'proof_of_payment']); // Optional
        });
    }

    public function down()
    {
        Schema::dropIfExists('deposits');
    }
}
