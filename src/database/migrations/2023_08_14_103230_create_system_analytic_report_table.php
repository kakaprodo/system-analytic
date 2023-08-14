<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemAnalyticReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_analytic_reports', function (Blueprint $table) {
            $table->id();
            $table->text('name')->comment('the analytic key name generated by custom data');
            $table->longText('value')->nullable()->comment('the serialized result');
            $table->string('analytic_type');
            $table->text('analytic_data')->comment('the serialized request inputs for this result');
            $table->string('report_scope')->comment('the period in which the result was provided');
            $table->string('group')->nullable()->comment('the group in which the analytic handler belongs to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_analytic_reports');
    }
}
