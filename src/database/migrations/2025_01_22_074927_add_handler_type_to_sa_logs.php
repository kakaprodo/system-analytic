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
        if (!Schema::hasColumn(Util::logTableName(), 'handler_type')) {
            Schema::table(Util::logTableName(), function (Blueprint $table) {
                $table->string('handler_type')
                    ->default('all')
                    ->comment('The analytic handler type on which the log should be displayed');
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
        if (Schema::hasColumn(Util::logTableName(), 'handler_type')) {
            Schema::table(Util::logTableName(), function (Blueprint $table) {
                $table->dropColumn('handler_type');
            });
        }
    }
};
