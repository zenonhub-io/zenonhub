<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models\Views;

use App\Domains\Nom\Models\Momentum;

class ViewLatestMomentum extends Momentum
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'view_latest_nom_momentums';
}
