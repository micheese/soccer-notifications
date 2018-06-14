<?php

namespace App\Console\Commands;

use App\Http\Services\ApplicationServiceInterface;
use Illuminate\Console\Command;

class FetchLiveGames extends Command
{
    /**
     * @var ApplicationServiceInterface
     */
    private $slackService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worldcup:live';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the inplay games';

    /**
     * FetchUpcomingGames constructor.
     * @param ApplicationServiceInterface $slackService
     */
    public function __construct(ApplicationServiceInterface $slackService)
    {
        $this->slackService = $slackService;
        parent::__construct();

    }

    /**
     * Execute the command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->slackService->postLiveResults();
    }
}
