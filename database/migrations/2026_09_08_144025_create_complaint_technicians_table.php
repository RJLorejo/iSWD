<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_technicians', function (Blueprint $table) {

            $table->id();

            $table->foreignId('complaint_id')
                ->constrained('complaints')
                ->cascadeOnDelete();

            $table->foreignId('technician_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('assignment_role', [
                'Lead',
                'Support'
            ])->default('Support');

            $table->enum('status', [
                'Assigned',
                'In Progress',
                'Completed',
                'Cancelled'
            ])->default('Assigned');

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique([
                'complaint_id',
                'technician_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_technicians');
    }
};
