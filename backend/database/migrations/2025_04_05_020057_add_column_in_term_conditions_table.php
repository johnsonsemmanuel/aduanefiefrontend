<?php

use App\Models\TermCondition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInTermConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('term_conditions', function (Blueprint $table) {
            $table->string('type')->default(TermCondition::TYPE_USER);
        });
        Schema::table('privacy_policies', function (Blueprint $table) {
            $table->string('type')->default(TermCondition::TYPE_USER);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('term_conditions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
