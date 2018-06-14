<?php

namespace App\Http\Services;

interface ApplicationServiceInterface
{
    /**
     *  Fetch upcoming games between $dateFrom and $dateTo and post them to Slack
     * @param string $dateFrom
     * @param string $dateTo
     * @return bool
     */
    public function postUpcomingGames($dateFrom, $dateTo);

	/**
	 * Fetch in-play games and post the result to Slack
	 * @return void
	 */
    public function postLiveResults();
}
