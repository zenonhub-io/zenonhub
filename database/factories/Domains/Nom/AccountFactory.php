<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Nom;

use App\Domains\Nom\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Account::class;

    public function definition(): array
    {
        $keyStore = \DigitalSloth\ZnnPhp\Wallet\KeyStore::random();

        return [
            'chain_id' => 1,
            'address' => $keyStore->getKeyPair()->address->toString(),
            'public_key' => base64_encode($keyStore->getKeyPair()->publicKey),
            'znn_balance' => 0,
            'qsr_balance' => 0,
        ];
    }
}
