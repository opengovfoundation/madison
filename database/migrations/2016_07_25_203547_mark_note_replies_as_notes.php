<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Annotation;

class MarkNoteRepliesAsNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $annotations = DB::table('annotations')->whereNotNull('data')->get();
        foreach ($annotations as $annotation) {
            $data = json_decode($annotation->data, true);

            if (empty($data['old_permalink_type']) || $data['old_permalink_type'] !== 'annsubcomment') {
                continue;
            }

            DB
                ::table('annotations')
                ->where('id', $annotation->id)
                ->update([
                    'annotation_subtype' => Annotation::SUBTYPE_NOTE,
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
        $annotations = DB::table('annotations')->whereNotNull('data')->get();
        foreach ($annotations as $annotation) {
            $data = json_decode($annotation->data, true);

            if (empty($data['old_permalink_type']) || $data['old_permalink_type'] !== 'annsubcomment') {
                continue;
            }

            DB
                ::table('annotations')
                ->where('id', $annotation->id)
                ->update([
                    'annotation_subtype' => null,
                ])
                ;
        }
    }
}
