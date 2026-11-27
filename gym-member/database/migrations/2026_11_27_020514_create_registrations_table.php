<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('package_id')
                ->constrained('packages')
                ->restrictOnDelete();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'active',
                'expired'
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
