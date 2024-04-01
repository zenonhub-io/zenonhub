<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models\Views;

use App\Domains\Nom\Models\AccountBlock;

class ViewLatestAccountBlock extends AccountBlock
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'view_latest_nom_account_blocks';
}
