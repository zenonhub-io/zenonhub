<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Nom;

use App\Http\Controllers\Api\ApiController;
use DigitalSloth\ZnnPhp\Zenon;

/**
 * ApiController
 * Contains methods for returning responses to the client, all API controllers need to extend from this
 */
abstract class NomController extends ApiController
{
    protected Zenon $znn;

    public function __construct()
    {
        parent::__construct();

        $this->znn = app(Zenon::class);
    }
}
