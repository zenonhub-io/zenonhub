# Zenonhub.io

## About
[Zenon Hub](https://zenonhub.io) is an explorer for the Zenon Network and provides a range of tools for interacting with and building on-top of the Network of Momentum.

## Requirements

This is built on [Laravel](https://laravel.com/)

- PHP 8.3
- MariaDB/MySQL
- NPM 18

## Install

Clone the repo and install composer dependencies `composer install`

Then install and compile frontend assets `npm i && npm run build`

## 3rd Party libraries
- Laravel Actions - https://github.com/lorisleiva/laravel-actions
- Livewire Charts - https://github.com/asantibanez/livewire-charts
- Livewire DataTables - https://github.com/rappasoft/laravel-livewire-tables
- Markable/Favorites - https://github.com/maize-tech/laravel-markable


## Setup

- Create a new database
- Copy the `example.env` to `.env` and fill in the details
- Next run migrations `php artisan migrate`
- Then seed the database `php artisan db:seed --class=DatabaseSeeder`
- Finally, run the genesis data `php artisan db:seed --class=GenesisSeeder`

## Indexer

The system uses queues for most processing, we use [horizon](https://laravel.com/docs/6.x/horizon) for managing queues on a production environment.
To run these locally run this command:
```bash
php artisan queue:work --queue=default,indexer
```

Next you'll need to run the indexer, on a server this is handled by a scheduled task every 10 seconds, locally you need to run:
```bash
php artisan indexer:run
```
