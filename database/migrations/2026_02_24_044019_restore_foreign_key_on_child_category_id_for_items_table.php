<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestoreForeignKeyOnChildCategoryIdForItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['child_category_id']);
        });

        DB::statement('
            ALTER TABLE items
            ADD CONSTRAINT items_child_category_id_foreign
            FOREIGN KEY (child_category_id)
            REFERENCES child_categories(id)
            ON DELETE CASCADE;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['child_category_id']);
            $table->foreign('child_category_id')->references('id')->on('child_categories');
        });
    }
}
