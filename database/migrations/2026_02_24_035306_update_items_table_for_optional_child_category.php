<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateItemsTableForOptionalChildCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('sub_category_id')->nullable()->after('id')->constrained('sub_categories')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE items MODIFY child_category_id BIGINT UNSIGNED NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn('sub_category_id');
        });

        DB::statement('ALTER TABLE items MODIFY child_category_id BIGINT UNSIGNED NOT NULL;');
    }
}
