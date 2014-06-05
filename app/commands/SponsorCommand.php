<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Database\Eloquent\Collection;

class SponsorCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'sponsor:assign';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Assign a sponsor to a document';

	/**
	 * Create a new command instance.
	 *
	 * @return void
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
		$docId = $this->argument('docId');
		
		$docs = new Collection();
		
		if(!is_null($docId)) {
			$doc = Doc::where('id', '=', $docId)->first();
			
			if(!$doc) {
				return $this->error("Invalid Document ID");
			}
			
			$docs->add($doc);
		} else {
			$rawDocs = DB::select(
				DB::raw(
					"SELECT *
					   FROM docs
					  WHERE id NOT IN (
						SELECT doc_id 
						  FROM doc_group
					 UNION ALL
						SELECT doc_id
						  FROM doc_user
					)"
				),
				array()
			);
			
			$docs = new Collection();
			
			foreach($rawDocs as $raw) {
				$obj = new Doc();
				
				foreach($raw as $key => $val) {
					$obj->$key = $val;
				}
				
				$docs->add($obj);
			}
		}
		
		$sponsors = Doc::getAllValidSponsors();
		
		foreach($docs as $doc) {
			$this->info("Document Title: {$doc->title}\n");
		
			foreach($sponsors as $key => $sponsor) {
				$opt = $key + 1;
				$this->info("$opt) {$sponsor['display_name']}");
			}
			
			$selected = (int)$this->ask("Please select a sponsor: ") -1;
			
			if(!isset($sponsors[$selected])) {
				$this->error("Invalid Selection");
				continue;
			}
			
			switch($sponsors[$selected]['sponsor_type']) {
				case 'individual':
					$doc->userSponsor()->sync(array($sponsors[$selected]['id']));
					$this->info("Assigned Document to Independent Sponsor");
					break;
				case 'group':
					$doc->groupSponsor()->sync(array($sponsors[$selected]['id']));
					$this->info("Assigned Document to Group Sponsor");
					break;
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('docId', InputArgument::OPTIONAL, 'An optional document to change the sponsor of.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
