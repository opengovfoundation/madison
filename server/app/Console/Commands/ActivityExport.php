<?php

namespace App\Console\Commands;

use App\Models\Annotation;
use App\Models\Comment;
use App\Models\Doc;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use URL;

class ActivityExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:export
                            {doc_id : Document id for exported activity}
                            {filename : Filename to save csv as}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Annotations and Comments';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $doc_id = $this->argument('doc_id');
        $filename = $this->argument('filename');

        $doc = Doc::where('id', $doc_id)->first();
        $this->info("Exporting activity for ".$doc->title);

        $annotations = Annotation::where('doc_id', $this->argument('doc_id'))->with('user')->with('comments')->get();
        $comments = Comment::where('doc_id', $this->argument('doc_id'))->with('user')->get();

        $headers = ["Created At", "Link", "Display Name", "Full Name", "Email", "Type", "Quote", "Text"];

        $toExport = [];

        foreach ($annotations as $annotation) {
            $annotationArray = [];

            $annotationArray['date'] = $annotation->created_at;
            $annotationArray['link'] = URL::to('/').$annotation->uri.'#annotation_'.$annotation->id;
            $annotationArray['display_name'] = $annotation->user->fname." ".substr($annotation->user->lname, 0, 1);
            $annotationArray['full_name'] = $annotation->user->fname." ".$annotation->user->lname;
            $annotationArray['email'] = $annotation->user->email;
            $annotationArray['type'] = 'Annotation';
            $annotationArray['quote'] = $annotation->quote;
            $annotationArray['text'] = $annotation->text;

            array_push($toExport, $annotationArray);

            foreach ($annotation->comments as $comment) {
                $user = User::find($comment->user_id);

                $commentArray = [];

                $commentArray['date'] = $comment->created_at;
                $commentArray['link'] = "";
                $commentArray['display_name'] = $user->fname." ".substr($user->lname, 0, 1);
                $commentArray['full_name'] = $user->fname." ".$user->lname;
                $commentArray['email'] = $user->email;
                $commentArray['type'] = "Annotation Comment";
                $commentArray['quote'] = "";
                $commentArray['text'] = $comment->text;

                array_push($toExport, $commentArray);
            }
        }

        foreach ($comments as $comment) {
            $commentArray = [];

            $commentArray['date'] = $comment->created_at;
            $commentArray['link'] = "";
            $commentArray['display_name'] = $comment->user->fname." ".substr($comment->user->lname, 0, 1);
            $commentArray['full_name'] = $comment->user->fname." ".$comment->user->lname;
            $commentArray['email'] = $comment->user->email;
            $commentArray['type'] = $comment->parent_id === null ? "Comment" : "Sub-comment";
            $commentArray['quote'] = "";
            $commentArray['text'] = $comment->text;

            array_push($toExport, $commentArray);
        }

        $this->info('Saving export to '.$filename);
        $fp = fopen($filename, 'w');
        fputcsv($fp, $headers);

        foreach ($toExport as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);
        $this->info('Done.');
    }
}
