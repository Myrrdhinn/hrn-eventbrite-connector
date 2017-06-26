<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;


class GetRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrn:getregs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Eventbrite Registrations from the database and post them to Pardot';

    /**
     * Attendee Class.
     *
     * @var AttendeesController
     */
    protected $attendes;

    /**
     * Create a new command instance.
     *
     * @return void
     */



    public function __construct(RegistrationsController $attendees)
    {
        parent::__construct();

        $this->attendees = $attendees;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info($this->attendees->index());
    }
}
