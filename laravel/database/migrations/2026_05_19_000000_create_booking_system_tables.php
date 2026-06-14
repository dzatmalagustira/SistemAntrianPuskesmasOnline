<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('polis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poli_id')->constrained('polis')->cascadeOnDelete();
            $table->string('name');
            $table->string('specialty');
            $table->string('photo')->nullable();
            $table->unsignedTinyInteger('experience_years')->default(1);
            $table->unsignedTinyInteger('daily_quota')->default(20);
            $table->timestamps();
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->enum('weekday', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->date('visit_date');
            $table->string('queue_number');
            $table->enum('status', ['menunggu', 'dipanggil', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->text('notes')->nullable();
            $table->string('estimated_wait')->nullable();
            $table->timestamps();
        });

        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->string('number');
            $table->enum('status', ['menunggu', 'dipanggil', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('queues');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('polis');
    }
};
