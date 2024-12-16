<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Util::shouldRunLogMigration()) {
            Schema::create((new (Util::logModel()))->getTable(), function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id')->comment('the owner of the report')->index();
                $table->string('tag')->comment('report reference');
                $table->string('action')->comment('log action')->nullable();
                $table->string('group')->comment('way to categorize reports')->nullable();
                $table->integer('value')->comment('report value');
                $table->string('identifier')->comment('can be anything to identify the action author, browser agent preferable');
                $table->text('key')->comment('combination of all previous columns');
                $table->json('payload')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new (Util::logModel()))->getTable());
    }
};
