<?php
namespace MadisonTasks;

use Rocketeer\Traits\Task;

class Migrate extends Task
{
  /**
   * Description of the Task
   *
   * @var string
   */
  protected $description = 'Migrates the database';

  /**
   * Executes the Task
   *
   * @return void
   */
  public function execute()
  {
    $this->command->info('Running migrations');
    $this->runForCurrentRelease('php artisan migrate');
  }
}
?>