<?php

use App\Services\UniqId;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddStrIdToAnnotations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('annotations', function ($table) {
            $table->string('str_id', 12)->nullable()->unique();
        });


        $annotations = DB::table('annotations')->get();
        foreach ($annotations as $annotation) {
            DB
                ::table('annotations')
                ->where('id', $annotation->id)
                ->update([
                    'str_id' => UniqId::genB64(),
                ])
                ;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('annotations', function ($table) {
            $table->dropUnique(['str_id']);
            $table->dropColumn('str_id');
        });
    }
}
