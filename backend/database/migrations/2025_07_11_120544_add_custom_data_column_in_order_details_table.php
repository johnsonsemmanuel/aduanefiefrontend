<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomDataColumnInOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->string('custom_status_note')->nullable();
            $table->string('custom_name')->nullable();
            $table->unsignedBigInteger('custom_unit_id')->nullable();
            $table->string('custom_photo')->nullable();
            $table->unsignedBigInteger('stock_id')->nullable()->change();
        });

        Schema::table('cart_details', function (Blueprint $table) {
            $table->string('custom_name')->nullable();
            $table->unsignedBigInteger('custom_unit_id')->nullable();
            $table->string('custom_photo')->nullable();
            $table->unsignedBigInteger('stock_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('custom_status');
            $table->dropColumn('custom_status_note');
            $table->dropColumn('custom_name');
            $table->dropColumn('custom_unit_id');
            $table->dropColumn('custom_photo');
        });

        Schema::table('cart_details', function (Blueprint $table) {
            $table->dropColumn('custom_name');
            $table->dropColumn('custom_unit_id');
            $table->dropColumn('custom_photo');
        });
    }
}
