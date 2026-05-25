<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perubahan_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->string('field');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejected_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perubahan_data');
    }
};
