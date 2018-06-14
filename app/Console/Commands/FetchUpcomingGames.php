<?php

namespace App\Console\Commands;

use App\Http\Services\SlackServiceInterface;
use Illuminate\Console\Command;

class FetchUpcomingGames extends Command
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
    protected $signature = 'worldcup:daily-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the games of the day and post it on Slack';

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
        $dateFrom = date("Y-m-d");
        $dateTo = date("Y-m-d") . 'T23:59';

//        $dateFrom = '2018-06-14';
//        $dateTo = '2018-06-14' . 'T23:59';
        $this->slackService->postUpcomingGames($dateFrom, $dateTo);
    }
}
