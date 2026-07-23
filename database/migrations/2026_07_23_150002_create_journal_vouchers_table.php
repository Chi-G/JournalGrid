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
        Schema::create('journal_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->unique();
            $table->date('voucher_date');
            $table->enum('type', ['general', 'cash', 'bank', 'adjustment']);
            $table->string('narration')->nullable();
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft');
            $table->bigInteger('total_debit_minor')->default(0);
            $table->bigInteger('total_credit_minor')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('reversal_of_id')->nullable()->constrained('journal_vouchers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_vouchers');
    }
};
