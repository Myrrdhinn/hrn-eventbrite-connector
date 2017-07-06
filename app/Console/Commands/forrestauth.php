<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Forrest; 

class forrestauth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forrest:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auth Forrest';

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
    public function handle()
    {
      return Forrest::refresh();
	
    }
}
