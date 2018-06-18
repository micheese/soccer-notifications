<?php

namespace App\Http\Services;

class ApplicationService implements ApplicationServiceInterface
{
    private $flags = [
        'Russia' => ':ru:',
        'Saudi Arabia' => ':flag-sa:',
        'Egypt' => ':flag-eg:',
        'Uruguay' => ':flag-uy:',
        'Portugal' => ':flag-pt:',
        'Spain' => ':flag-es:',
        'Morocco' => ':flag-ma:',
        'Iran' => ':flag-ir:',
        'France' => ':flag-fr:',
        'Australia' => ':flag-au:',
        'Peru' => ':flag-pe:',
        'Denmark' => ':flag-dk:',
        'Argentina' => ':flag-ar:',
        'Iceland' => ':flag-is:',
        'Croatia' => '🇭🇷',
        'Nigeria' => ':flag-ng:',
        'Brazil' => ':flag-br:',
        'Switzerland' => ':flag-ch:',
        'Costa Rica' => ':flag-cr:',
        'Serbia' => ':flag-rs:',
        'Germany' => ':flag-de:',
        'Mexico' => ':flag-mx:',
        'Sweden' => ':flag-se:',
        'South Korea' => ':flag-kr:',
        'Belgium' => ':flag-be:',
        'Panama' => ':flag-pa:',
        'Tunisia' => ':flag-tn:',
        'England' => ':flag-england:',
        'Poland' => ':flag-pl:',
        'Senegal' => ':flag-sn:',
        'Colombia' => ':flag-co:',
        'Japan' => ':flag-jp:',
    ];

    private $sportDeerToken;

    public function __construct()
    {
        $this->refreshToken();
    }


    /**
     * @inheritdoc
     */
    public function postUpcomingGames($dateFrom, $dateTo)
    {
        // Fetch upcoming games
        $url = 'https://api.sportdeer.com/v1/seasons/296/upcoming?dateFrom='. $dateFrom .'&dateTo='. $dateTo .'&populate=countries&access_token=' . $this->sportDeerToken;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = json_decode(curl_exec($ch), true);

        curl_close($ch);
        
        if (array_get($result, 'status') == 401)
        {
            $this->refreshToken();
            $this->postUpcomingGames($dateTo, $dateFrom);
        }

        // Post Message to the slack URL
        $ch = curl_init('https://slack.com/api/chat.postMessage');

        if ($fixtures = array_get($result, 'docs'))
        {
            foreach ($fixtures as $fixture)
            {
                $homeTeam = array_get($fixture, 'team_season_home_name');
                $homeTeamFlag = array_get($this->flags, $homeTeam, ':flags:');

                $awayTeam = array_get($fixture, 'team_season_away_name');
                $awayTeamFlag = array_get($this->flags, $awayTeam, ':flags:');

                $gameTime = new \DateTime(array_get($fixture, 'schedule_date'), new \DateTimeZone('GMT+3'));
                $gameTime->setTimezone(new \DateTimeZone('ADT'));
                $gameTime = $gameTime->format('H:i');

                $text = "Today will play : $homeTeam $homeTeamFlag vs $awayTeam $awayTeamFlag at $gameTime";

                $data = http_build_query([
                    'token' => env('SLACK_TOKEN'),
                    'channel' => env('SLACK_CHANNEL'),
                    'text' => $text,
                    'username' => env('SLACK_USERNAME'),
                    'icon_url' => env('SLACK_ICON_URL')
                ]);


                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_exec($ch);
            }
        }
	    curl_close($ch);
    }

	/**
	 * @inheritdoc
	 */
    public function postLiveResults()
    {
        // Fetch live results
        $url = 'https://api.sportdeer.com/v1/seasons/296/inplay?access_token=' . $this->sportDeerToken;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = json_decode(curl_exec($ch), true);

        curl_close($ch);

        if (array_get($result, 'status') == 401)
        {
            $this->refreshToken();
        }

        // Post Message to the slack URL
        $liveResults = json_decode(file_get_contents('app/Http/Services/live-results.json', FILE_USE_INCLUDE_PATH), true);

        if ($inplays = array_get($result, 'docs'))
        {
            foreach ($inplays as $inplay)
            {
                if (!array_get($liveResults['live'], $inplay['_id']))
                {
                    $liveResults['live'][$inplay['_id']] = $inplay;
                    file_put_contents('app/Http/Services/live-results.json', json_encode($liveResults));
                }

                $homeScore = $liveResults['live'][$inplay['_id']]['number_goal_team_home'];
                $awayScore = $liveResults['live'][$inplay['_id']]['number_goal_team_away'];

                $homeTeam = array_get($inplay, 'team_season_home_name');
                $homeTeamFlag = array_get($this->flags, $homeTeam);
                $awayTeam = array_get($inplay, 'team_season_away_name');
                $awayTeamFlag = array_get($this->flags, $awayTeam);

                $liveScoreHome = $inplay['number_goal_team_home'];
                $liveScoreAway = $inplay['number_goal_team_away'];

                if ($liveScoreHome > $homeScore)
                {
                    $text = "Goal for $homeTeam $homeTeamFlag !!! $liveScoreHome $homeTeamFlag : $liveScoreAway $awayTeamFlag";
                }

                if ($liveScoreAway > $awayScore)
                {
                    $text = "Goal for $awayTeam $awayTeamFlag !!! $liveScoreHome $homeTeamFlag : $liveScoreAway $awayTeamFlag";
                }

                if (isset($text))
                {
                    $ch = curl_init('https://slack.com/api/chat.postMessage');
                    $data = http_build_query([
                        'token' => env('SLACK_TOKEN'),
                        'channel' => env('SLACK_CHANNEL'),
                        'text' => $text,
                        'username' => env('SLACK_USERNAME'),
                        'icon_url' => env('SLACK_ICON_URL')
                    ]);


                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_exec($ch);
                    curl_close($ch);

                    $liveResults['live'][$inplay['_id']] = $inplay;
                    file_put_contents('app/Http/Services/live-results.json', json_encode($liveResults));
                }
            }
        }
    }

    /**
     * Refresh SportDeer auth token
     */
    private function refreshToken()
    {
        $ch = curl_init('https://api.sportdeer.com/v1/accessToken?refresh_token=' . env('SPORTDEER_REFRESH_TOKEN'));

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $this->sportDeerToken = array_get($result, 'new_access_token');
    }
}
