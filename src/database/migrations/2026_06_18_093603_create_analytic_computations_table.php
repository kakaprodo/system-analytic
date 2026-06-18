<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kakaprodo\SystemAnalytic\Utilities\Util;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Util::shouldRunComputationMigration()) return;

        Schema::create('analytic_computations', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('tenant');
            $table->string('sub_tenant')->nullable();
            $table->string('reference')->nullable();
            $table->dateTime('period');
            $table->float('computed_value');
            $table->json('payload')->nullable();
            $table->string('data_key');
            $table->timestamps();

            $table->index(['category', 'tenant', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytic_computations');
    }
};
