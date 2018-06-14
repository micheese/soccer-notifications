# Live feed soccer
This application will fetch live soccer games and will post the results to a Slack channel/group of your choice.

## Requirements
This application runs on Laravel 5.4 and needs PHP 5.6 at least.
The live data are fetched from `https://www.sportdeer.com/` and the application is currently configured to fetch the World Cup 2018 in Russia.
Feel free to update it to your needs.

## Installation
 - [ ] Clone this repository
 - [ ] Run `composer install`
 - [ ] Update `.env` with Slack information
 - [ ] Add the following Cron entry to your server : `* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1`
 
 ## Available commands
 - `php artisan worldcup:live` fetches current games
 - `php artisan worldcup:daily-schedule` fetches daily schedule
 
 ### Notes
 This was a pet project and that code isn't optimized to run on large scale. Take it as is.
 It will be my pleasure to answer any question at `michael.chemani@gmail.com`
