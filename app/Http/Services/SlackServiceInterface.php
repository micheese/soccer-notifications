<?php

namespace App\Http\Services;

interface SlackServiceInterface
{
    /**
     * Will fetch upcoming games between $dateFrom and $dateTo and post them to Slack
     * @param string $dateFrom
     * @param string $dateTo
     * @return bool
     */
    public function postUpcomingGames($dateFrom, $dateTo);

    public function postLiveResults();
}
