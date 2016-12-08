<?php

namespace App\Console\Commands;

use App\Models\Annotation;
use App\Models\Comment;
use App\Models\Doc;
use App\Services;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

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

    protected $commentService;

    /**
     * Create a new command instance.
     */
    public function __construct(Services\Comments $commentService)
    {
        parent::__construct();
        $this->commentService = $commentService;
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

        $comments = $doc->all_comments;

        $csv = $this->commentService->toCsv($comments);
        file_put_contents($filename, $csv);

        $this->info('Done.');
    }
}
