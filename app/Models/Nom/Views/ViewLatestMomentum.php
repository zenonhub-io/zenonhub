<?php

declare(strict_types=1);

namespace App\Models\Nom\Views;

use App\Models\Nom\Momentum;

class ViewLatestMomentum extends Momentum
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'view_latest_nom_momentums';
}
