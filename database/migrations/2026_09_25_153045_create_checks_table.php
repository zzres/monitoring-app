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
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('probe_id')->constrained()->onDelete('cascade');
            $table->boolean('is_up');
            $table->integer('response_time_ms')->nullable();
            $table->integer('status_code')->nullable(); // pour HTTP uniquement
            $table->text('error_message')->nullable();
            $table->timestamp('checked_at')->useCurrent();//Date de l'execution de la vérification
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};
