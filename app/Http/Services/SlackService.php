<?php

namespace App\Http\Services;

class SlackService implements SlackServiceInterface
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
        'Brasil' => ':flag-br:',
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
        'England' => ':flag-en:',
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

                $gameTime = new \DateTime(array_get($fixture, 'schedule_date'), new \DateTimeZone('UTC'));
                $gameTime->setTimezone(new \DateTimeZone('EST'));
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
                curl_close($ch);
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
