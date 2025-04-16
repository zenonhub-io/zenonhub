<?php

declare(strict_types=1);

namespace App\Livewire;

use App\DataTransferObjects\SearchResultDTO;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Momentum;
use App\Models\Nom\Pillar;
use App\Models\Nom\Token;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class SiteSearch extends Component
{
    public string $search = '';

    private ?int $totalResults = null;

    private array $results = [
        'transactions' => [],
        'momentums' => [],
        'accounts' => [],
        'pillars' => [],
        'tokens' => [],
        'projects' => [],
        'phases' => [],
    ];

    public function render(): View
    {
        $this->preformSearch();

        return view('livewire.site-search', [
            'totalResults' => $this->totalResults,
            'results' => $this->results,
        ]);
    }

    private function preformSearch(): void
    {
        if (empty($this->search)) {
            return;
        }

        $this->results['momentums'] = $this->searchMomentums();

        if (strlen($this->search) >= 4) {
            $this->results['transactions'] = $this->searchTransactions();
            $this->results['accounts'] = $this->searchAccounts();
        }

        $this->results['pillars'] = $this->searchPillars();
        $this->results['tokens'] = $this->searchTokens();
        $this->results['projects'] = $this->searchProjects();
        $this->results['phases'] = $this->searchPhases();
        $this->totalResults = $this->getTotalResultsCount();
    }

    private function searchTransactions(): Collection
    {
        return AccountBlock::where('hash', $this->search)->get()
            ->map(fn (AccountBlock $tx) => SearchResultDTO::from([
                'group' => 'transactions',
                'title' => $tx->hash,
                'link' => route('explorer.transaction.detail', ['hash' => $tx->hash]),
            ]));
    }

    private function searchMomentums(): Collection
    {
        $query = Momentum::where('height', $this->search);

        if (strlen($this->search) >= 4) {
            $query->orWhere('hash', $this->search);
        }

        return $query->get()
            ->map(fn (Momentum $momentum) => SearchResultDTO::from([
                'group' => 'momentums',
                'title' => $momentum->hash,
                'comment' => sprintf('Height #%s', $momentum->height),
                'link' => route('explorer.momentum.detail', ['hash' => $momentum->hash]),
            ]));
    }

    private function searchAccounts(): Collection
    {
        return Account::search($this->search)->get()
            ->map(fn (Account $account) => SearchResultDTO::from([
                'group' => 'accounts',
                'title' => $account->name ?: $account->address,
                'link' => route('explorer.account.detail', ['address' => $account->address]),
            ]));
    }

    private function searchPillars(): Collection
    {
        return Pillar::search($this->search)->get()
            ->map(fn (Pillar $pillar) => SearchResultDTO::from([
                'group' => 'pillars',
                'title' => $pillar->name,
                'link' => route('pillar.detail', ['slug' => $pillar->slug]),
            ]));
    }

    private function searchTokens(): Collection
    {
        return Token::search($this->search)->get()
            ->map(fn (Token $token) => SearchResultDTO::from([
                'group' => 'pillars',
                'title' => $token->name,
                'comment' => $token->token_standard,
                'link' => route('explorer.token.detail', ['zts' => $token->token_standard]),
            ]));
    }

    private function searchProjects(): Collection
    {
        $searchResults = AcceleratorProject::search($this->search)->get();
        $hashResults = AcceleratorProject::where('hash', $this->search)->get();

        return $searchResults->merge($hashResults)
            ->map(fn (AcceleratorProject $project) => SearchResultDTO::from([
                'group' => 'projects',
                'title' => $project->name,
                'comment' => $project->status->label(),
                'link' => route('accelerator-z.project.detail', ['hash' => $project->hash]),
            ]));
    }

    private function searchPhases(): Collection
    {
        $searchResults = AcceleratorPhase::search($this->search)->get();
        $hashResults = AcceleratorPhase::where('hash', $this->search)->get();

        return $searchResults->merge($hashResults)
            ->map(function (AcceleratorPhase $phase) {
                $phase->load('project');

                return SearchResultDTO::from([
                    'group' => 'phases',
                    'title' => $phase->name,
                    'comment' => $phase->project->name,
                    'link' => route('accelerator-z.phase.detail', ['hash' => $phase->hash]),
                ]);
            });
    }

    private function getTotalResultsCount(): int
    {
        return collect(array_values($this->results))->map(fn ($items) => count($items))->sum();
    }
}
