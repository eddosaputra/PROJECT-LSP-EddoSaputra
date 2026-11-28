<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (! Schema::hasColumn('packages', 'name')) {
                $table->string('name');
            }
            if (! Schema::hasColumn('packages', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('packages', 'price')) {
                $table->decimal('price', 12, 2);
            }
            if (! Schema::hasColumn('packages', 'duration_days')) {
                $table->unsignedInteger('duration_days');
            }
            if (! Schema::hasColumn('packages', 'image')) {
                $table->string('image')->nullable();
            }
            if (! Schema::hasColumn('packages', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });

        Schema::table('registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('registrations', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('registrations', 'package_id')) {
                $table->foreignId('package_id')->constrained('packages')->restrictOnDelete();
            }
            if (! Schema::hasColumn('registrations', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (! Schema::hasColumn('registrations', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (! Schema::hasColumn('registrations', 'status')) {
                $table->string('status')->default('pending');
            }
            if (! Schema::hasColumn('registrations', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'registration_id')) {
                $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 12, 2);
            }
            if (! Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (! Schema::hasColumn('payments', 'proof_image')) {
                $table->string('proof_image')->nullable();
            }
            if (! Schema::hasColumn('payments', 'status')) {
                $table->string('status')->default('pending');
            }
            if (! Schema::hasColumn('payments', 'paid_at')) {
                $table->dateTime('paid_at')->nullable();
            }
            if (! Schema::hasColumn('payments', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        Schema::table('announcements', function (Blueprint $table) {
            if (! Schema::hasColumn('announcements', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('announcements', 'title')) {
                $table->string('title');
            }
            if (! Schema::hasColumn('announcements', 'content')) {
                $table->text('content');
            }
            if (! Schema::hasColumn('announcements', 'image')) {
                $table->string('image')->nullable();
            }
            if (! Schema::hasColumn('announcements', 'is_published')) {
                $table->boolean('is_published')->default(true);
            }
        });
    }

    public function down(): void
    {
        // The original create-table migrations own these columns.
    }
};
