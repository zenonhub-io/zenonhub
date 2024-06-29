<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Pillar;
use App\Domains\Nom\Models\Sentinel;
use App\Domains\Nom\Models\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap
{
    use AsAction;

    public string $commandSignature = 'site:generate-sitemap';

    public function handle(): void
    {
        $file = 'app/sitemap/sitemap.xml';
        $this->checkFileExists($file);

        Sitemap::create()
            ->add($this->addItem('home'))
            ->add($this->addItem('donate'))
            ->add($this->addItem('sponsor'))
            ->add($this->addItem('terms'))
            ->add($this->addItem('policy'))

            ->add($this->addItem('pillars'))
            ->add(Pillar::all())

            ->add($this->addItem('sentinels'))
            ->add(Sentinel::all())

            ->add($this->addItem('accelerator-z'))
            ->add(AcceleratorProject::all())
            ->add(AcceleratorPhase::all())

            ->add($this->addItem('explorer'))
            ->add($this->addItem('explorer.momentums'))
            ->add($this->addItem('explorer.transactions'))
            ->add($this->addItem('explorer.accounts'))
            ->add(Account::whereEmbedded()->get()->all())
            ->add($this->addItem('explorer.tokens'))
            ->add(Token::whereNetwork()->get()->all())
            ->add($this->addItem('explorer.stakes'))
            ->add($this->addItem('explorer.plasma'))

            ->add($this->addItem('stats.bridge'))
            ->add($this->addItem('stats.public-nodes'))
            ->add($this->addItem('stats.accelerator-z'))

            ->add($this->addItem('tools.plasma-bot'))
            ->add($this->addItem('tools.api-playground'))
            ->add($this->addItem('tools.verify-signature'))
            ->add($this->addItem('tools.broadcast-message'))

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
