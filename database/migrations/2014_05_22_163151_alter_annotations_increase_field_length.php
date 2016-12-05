<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAnnotationsIncreaseFieldLength extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `annotations` MODIFY COLUMN `quote` TEXT');
        DB::statement('ALTER TABLE `annotations` MODIFY COLUMN `text` TEXT');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `annotations` MODIFY COLUMN `quote` VARCHAR(255)');
        DB::statement('ALTER TABLE `annotations` MODIFY COLUMN `text` VARCHAR(255)');
    }
}
