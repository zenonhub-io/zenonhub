<?php

namespace App\Actions;

use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Account;
use App\Models\Nom\Pillar;
use App\Models\Nom\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap
{
    public function execute(): void
    {
        $file = 'app/sitemap/sitemap.xml';
        $this->checkFileExists($file);

        Sitemap::create()
            ->add($this->addItem('home'))
            ->add($this->addItem('donate'))

            ->add($this->addItem('explorer.overview'))
            ->add($this->addItem('explorer.momentums'))
            ->add($this->addItem('explorer.transactions'))
            ->add($this->addItem('explorer.accounts'))
            ->add(Account::whereIn('address', array_keys(Account::EMBEDDED_CONTRACTS))->get()->all())
            ->add($this->addItem('explorer.tokens'))
            ->add(Token::whereIn('token_standard', [Token::ZTS_ZNN, Token::ZTS_QSR, Token::ZTS_LP_ETH])->get()->all())
            ->add($this->addItem('explorer.staking'))
            ->add($this->addItem('explorer.fusions'))

            ->add($this->addItem('pillars.overview'))
            ->add(Pillar::all())

            ->add($this->addItem('az.overview'))
            ->add(AcceleratorProject::all())
            ->add(AcceleratorPhase::all())

            ->add($this->addItem('tools.overview'))
            ->add($this->addItem('tools.plasma-bot'))
            ->add($this->addItem('tools.api-playground'))
            ->add($this->addItem('tools.verify-signature'))
            ->add($this->addItem('tools.broadcast-message'))

            ->add($this->addItem('stats.overview'))
            ->add($this->addItem('stats.nodes'))
            ->add($this->addItem('stats.accelerator'))

            ->add($this->addItem('services.overview'))
            ->add($this->addItem('services.public-nodes'))
            ->add($this->addItem('services.plasma-bot'))
            ->add($this->addItem('services.whale-alerts'))
            ->add($this->addItem('services.bridge-alerts'))

            ->writeToFile(storage_path($file));
    }

    private function addItem($route): Url
    {
        return Url::create(route($route))
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY);
    }

    private function checkFileExists($file): void
    {
        $file = storage_path($file);
        if (! file_exists($file)) {
            Storage::makeDirectory('sitemap');
            Storage::put('app/sitemap/sitemap.xml', '');
        }
    }
}
