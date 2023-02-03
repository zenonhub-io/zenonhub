# Zenonhub.io

## About
[Zenon Hub](https://zenonhub.io) is an explorer for the Zenon Network and provides a range of tools for interacting with and building on-top of the Network of Momentum.

## Requirements

This is built on [Laravel](https://laravel.com/) 

- PHP 8.1
- MariaDB 10

## Install

Clone the repo and install composer dependencies `composer install`

Then install and compile frontend assets `npm i && npm mix`

## Setup

- Create a new database
- Copy the `example.env` to `.env` and fill in the details
- Next run migrations `php artisan migrate`
- Then sync initial network data `php artisan zenon:sync tokens pillars sentinels az`

## Indexing and Explorer

The system uses queues for most processing, on a server configure a [supervisor](https://laravel.com/docs/9.x/queues#supervisor-configuration) task to run these two queues:
```bash
artisan queue:work --queue=default
artisan queue:work --queue=indexer --tries=25 --backoff=10
```

Next you'll need to run the indexer, again if running on a server setup supervisor for this command as well:
```bash
php artisan zenon:index
```
