<?php

namespace App\Console\Commands;

use App\Http\Services\SlackServiceInterface;
use Illuminate\Console\Command;

class FetchLiveGames extends Command
{
    /**
     * @var SlackServiceInterface
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
     * @param SlackServiceInterface $slackService
     */
    public function __construct(SlackServiceInterface $slackService)
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
