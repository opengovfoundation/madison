<?php

namespace App\Services;

use League\Csv\Writer;

class Comments
{
    /**
     * @param [App\Models\Comment] $comments
     *
     * @return League\Csv\Writer
     */
    public function toCsv($comments)
    {
        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        $fields = [
            'first_name',
            'last_name',
            'email',
            'quote',
            'text',
            'type',
            'created_at'
        ];

        // Headings.
        $csv->insertOne($fields);

        foreach ($comments as $comment) {
            $row = [
                'first_name' => $comment->user->fname,
                'last_name' => $comment->user->lname,
                'email' => $comment->user->email,
                'quote' => !empty($comment->data['quote']) ? $comment->data['quote'] : null,
                'text' => $comment->annotationType->content,
                'type' => $comment->isNote() ? 'note' : 'comment',
                'created_at' => $comment->created_at->toRfc3339String(),
            ];

            $csv->insertOne($row);
        }

        return $csv;
    }
}
