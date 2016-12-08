<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// use DB;

class ActionView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create a rather complicated view.
        DB::statement(
<<<EOL
        CREATE VIEW doc_actions AS
            SELECT
                comments.id AS id,
                null AS search_id,
                comments.user_id AS user_id,
                comments.doc_id AS doc_id,
                comments.parent_id AS parent_id,
                null AS quote,
                comments.text AS text,
                null AS uri,
                comments.seen AS seen,
                comments.created_at AS created_at,
                comments.updated_at AS updated_at,
                null AS deleted_at,
                'comment' AS type
            FROM comments
        UNION
            SELECT
                annotations.id AS id,
                annotations.search_id AS search_id,
                annotations.user_id AS user_id,
                annotations.doc_id AS doc_id,
                null AS parent_id,
                annotations.quote AS quote,
                annotations.text AS text,
                annotations.uri AS uri,
                annotations.seen AS seen,
                annotations.created_at AS created_at,
                annotations.updated_at AS updated_at,
                annotations.deleted_at AS deleted_at,
                'annotation' AS type
            FROM annotations
        UNION
            SELECT
                annotation_comments.id AS id,
                null AS search_id,
                annotation_comments.user_id AS user_id,
                annotations.doc_id AS doc_id,
                annotation_comments.annotation_id AS parent_id,
                null AS quote,
                annotation_comments.text AS text,
                null AS uri,
                null AS seen,
                annotation_comments.created_at AS created_at,
                annotation_comments.updated_at AS updated_at,
                annotation_comments.deleted_at AS deleted_at,
                'annotation_comment' AS type
            FROM annotation_comments
            LEFT JOIN annotations on annotations.id = annotation_comments.annotation_id
        ;
EOL
);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop our view
        DB::statement('DROP VIEW doc_actions');
    }
}
