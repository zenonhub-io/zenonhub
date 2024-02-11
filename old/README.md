# Zenonhub.io

## About
[Zenon Hub](https://zenonhub.io) is an explorer for the Zenon Network and provides a range of tools for interacting with and building on-top of the Network of Momentum.

## Requirements

This is built on [Laravel](https://laravel.com/) 

- PHP 8.2
- MariaDB/MySQL

## Install

Clone the repo and install composer dependencies `composer install`

Then install and compile frontend assets `npm i && npm mix`

## Setup

- Create a new database
- Copy the `example.env` to `.env` and fill in the details
- Next run migrations `php artisan migrate`
- Then seed the database `php artisan db:seed --class=DatabaseSeeder`
- Finally run the genesis data `php artisan db:seed --class=GenesisSeeder`

## Indexer

The system uses queues for most processing, we use [horizon](https://laravel.com/docs/6.x/horizon) for managing queues on a production environment.
To run these locally run this command:
```bash
artisan queue:work --queue=default,indexer
```

Next you'll need to run the indexer, it will index the network to the current height and exit. On a server configure [short-schedule](https://github.com/spatie/laravel-short-schedule) and the indexer will be run every 10 seconds. Or locally run:
```bash
php artisan zenon:index
```
