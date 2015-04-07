<?php

use Illuminate\Database\Migrations\Migration;

class AlterDocMetaMetaKeyAsText extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('ALTER TABLE `doc_meta` MODIFY COLUMN `meta_value` TEXT');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('ALTER TABLE `doc_meta` MODIFY COLUMN `meta_value` VARCHAR(255)');
    }
}
