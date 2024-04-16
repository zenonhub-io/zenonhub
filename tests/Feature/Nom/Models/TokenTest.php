<?php

declare(strict_types=1);

use App\Domains\Nom\Models\Token;

uses()->group('nom', 'nom-models', 'token');

beforeEach(function () {
    $this->token = new Token;
});

test('getDisplayAmount returns correct value with for null values', function () {

    $this->token->decimals = 8;
    $result = $this->token->getDisplayAmount(null);

    expect($result)->toBe(0);

});

test('getDisplayAmount returns correct value with for 0 values', function () {

    $this->token->decimals = 8;
    $result = $this->token->getDisplayAmount(0);

    expect($result)->toBe(0.0);

});

test('getDisplayAmount returns correct value with for whole numbers', function () {

    $this->token->decimals = 8;
    $result = $this->token->getDisplayAmount('1000000000000000');

    expect($result)->toBe(10000000.0);

});

test('getDisplayAmount returns correct value with for decimal', function () {

    $this->token->decimals = 8;
    $result = $this->token->getDisplayAmount('1000000000000001');

    expect($result)->toBe(10000000.00000001);

});

test('getDisplayAmount returns correct value for token with 0 decimals', function () {

    $this->token->decimals = 0;
    $result = $this->token->getDisplayAmount('1000000000000001');

    expect($result)->toBe(1000000000000001);

});

test('getFormattedAmount returns correct value with for null values', function () {

    $this->token->decimals = 8;
    $result = $this->token->getFormattedAmount(null);

    expect($result)->toBe('-');

});

test('getFormattedAmount returns correct value with for 0 values', function () {

    $this->token->decimals = 8;
    $result = $this->token->getFormattedAmount(0);

    expect($result)->toBe('0');

});

test('getFormattedAmount returns correct value with for whole numbers', function () {

    $this->token->decimals = 8;
    $result = $this->token->getFormattedAmount('1000000000000000');

    expect($result)->toBe('10,000,000');

});

test('getFormattedAmount returns correct value with for decimal', function () {

    $this->token->decimals = 8;
    $result = $this->token->getFormattedAmount('1000000000000001');

    expect($result)->toBe('10,000,000.00000001');

});

test('getFormattedAmount returns correct value for token with 0 decimals', function () {

    $this->token->decimals = 0;
    $result = $this->token->getFormattedAmount('1000000000000001');

    expect($result)->toBe('1,000,000,000,000,001');

});
