<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCommentsAutoIncrementing extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `annotation_comments` CHANGE COLUMN `id` `id` INT(10) unsigned auto_increment primary key');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `annotation_comments` CHANGE COLUMN `id` `id` INT(10) unsigned');
    }
}
