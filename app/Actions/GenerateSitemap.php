<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Account;
use App\Models\Nom\Pillar;
use App\Models\Nom\Sentinel;
use App\Models\Nom\Token;
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
            ->add($this->addItem('info'))
            ->add($this->addItem('donate'))
            ->add($this->addItem('advertise'))
            ->add($this->addItem('policy'))
            // ->add($this->addItem('terms'))

            ->add($this->addItem('pillar.list'))
            ->add($this->addItem('pillar.list', ['tab' => 'active']))
            ->add($this->addItem('pillar.list', ['tab' => 'inactive']))
            ->add($this->addItem('pillar.list', ['tab' => 'revoked']))
            ->add(Pillar::all())

//            ->add($this->addItem('sentinel.list'))
//            ->add(Sentinel::all())

            ->add($this->addItem('accelerator-z.list'))
            ->add($this->addItem('accelerator-z.list', ['tab' => 'open']))
            ->add($this->addItem('accelerator-z.list', ['tab' => 'accepted']))
            ->add($this->addItem('accelerator-z.list', ['tab' => 'completed']))
            ->add($this->addItem('accelerator-z.list', ['tab' => 'rejected']))
            ->add(AcceleratorProject::all())
            ->add(AcceleratorPhase::all())

            ->add($this->addItem('explorer.overview'))
            ->add($this->addItem('explorer.momentum.list'))
            ->add($this->addItem('explorer.block.list'))
            ->add($this->addItem('explorer.account.list'))
            ->add($this->addItem('explorer.account.list', ['tab' => 'contracts']))
            ->add($this->addItem('explorer.account.list', ['tab' => 'pillars']))
            ->add($this->addItem('explorer.account.list', ['tab' => 'sentinels']))
            ->add($this->addItem('explorer.account.list', ['tab' => 'favorites']))
            ->add(Account::whereEmbedded()->get()->all())
            ->add($this->addItem('explorer.token.list'))
            ->add($this->addItem('explorer.token.list', ['tab' => 'network']))
            ->add($this->addItem('explorer.token.list', ['tab' => 'user']))
            ->add(Token::whereNetwork()->get()->all())
            ->add($this->addItem('explorer.bridge.list'))
            ->add($this->addItem('explorer.bridge.list', ['tab' => 'outbound']))
            ->add($this->addItem('explorer.bridge.list', ['tab' => 'networks']))
            ->add($this->addItem('explorer.stake.list'))
            ->add($this->addItem('explorer.stake.list', ['tab' => 'znn-eth-lp']))
            ->add($this->addItem('explorer.plasma.list'))

            ->add($this->addItem('stats.bridge'))
            ->add($this->addItem('stats.bridge', ['tab' => 'security']))
            ->add($this->addItem('stats.bridge', ['tab' => 'actions']))
            ->add($this->addItem('stats.bridge', ['tab' => 'orchestrators']))
            ->add($this->addItem('stats.bridge', ['tab' => 'affiliates']))
            ->add($this->addItem('stats.public-nodes'))
            ->add($this->addItem('stats.public-nodes', ['tab' => 'list']))
            ->add($this->addItem('stats.accelerator-z'))
            ->add($this->addItem('stats.accelerator-z', ['tab' => 'engagement']))
            ->add($this->addItem('stats.accelerator-z', ['tab' => 'contributors']))

            ->add($this->addItem('tools.plasma-bot'))
            ->add($this->addItem('tools.api-playground'))
            ->add($this->addItem('tools.verify-signature'))
            // ->add($this->addItem('tools.broadcast-message'))

            ->add($this->addItem('services.public-nodes'))
//            ->add($this->addItem('services.plasma-bot'))
//            ->add($this->addItem('services.whale-alerts'))
//            ->add($this->addItem('services.bridge-alerts'))

            ->writeToFile(storage_path($file));
    }

    private function addItem($route, $params = []): Url
    {
        return Url::create(route($route, $params))
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY);
    }

    private function checkFileExists($file): void
    {
        $file = storage_path($file);
        if (! file_exists($file)) {
            Storage::makeDirectory('sitemap');
            Storage::put('sitemap/sitemap.xml', '');
        }
    }
}
