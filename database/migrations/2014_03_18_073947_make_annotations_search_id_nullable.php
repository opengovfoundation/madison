<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeAnnotationsSearchIdNullable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `annotations` CHANGE COLUMN `search_id` `search_id` VARCHAR(255) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `annotations` CHANGE COLUMN `search_id` VARCHAR(255) IS NOT NULL');
    }

}
