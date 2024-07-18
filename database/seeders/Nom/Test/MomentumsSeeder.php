<?php

declare(strict_types=1);

namespace Database\Seeders\Nom\Test;

use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Models\Momentum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MomentumsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Momentum::truncate();
        Schema::enableForeignKeyConstraints();

        $momentumJson = Storage::json('nom-json/test/momentums.json');
        $momentumDTOs = MomentumDTO::collect($momentumJson, Collection::class);

        $momentumDTOs->each(function ($momentumDTO) {
            Momentum::insert([
                'chain_id' => $momentumDTO->chainIdentifier,
                'producer_account_id' => load_account($momentumDTO->producer)->id,
                'producer_pillar_id' => null,
                'version' => $momentumDTO->version,
                'height' => $momentumDTO->height,
                'hash' => $momentumDTO->hash,
                'data' => $momentumDTO->data,
                'created_at' => $momentumDTO->timestamp,
            ]);
        });
    }
}
