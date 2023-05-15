<?php

namespace App\Http\Livewire\Stats;

use App\Http\Livewire\ChartTrait;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Account;
use App\Models\Nom\Pillar;
use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Livewire\WithPagination;

class Accelerator extends Component
{
    use WithPagination;
    use ChartTrait;

    public string $tab = 'funding';

    public Account $acceleratorContract;

    public array $azFundingZnn;

    public array $azFundingQsr;

    public array $azProjectTotals;

    protected $paginationTheme = 'bootstrap';

    protected Paginator $engagementData;

    public string $engagementSort = 'az_engagement';

    public string $engagementOrder = 'desc';

    protected $queryString = [
        'tab' => ['except' => 'funding'],
    ];

    protected $listeners = ['tabChange'];

    public function tabChange($tab = 'funding')
    {
        $this->tab = $tab;
    }

    public function render()
    {
        $this->loadFundingData();
        $this->loadProjectData();
        $this->loadEngagementData();

        return view('livewire.stats.accelerator', [
            'engagementData' => $this->engagementData,
        ]);
    }

    public function engagementSortBy($field)
    {
        $this->engagementOrder = $this->engagementSort === $field
            ? $this->engagementOrder === 'asc'
                ? 'desc'
                : 'asc'
            : 'asc';

        $this->engagementSort = $field;
    }

    private function loadFundingData()
    {
        $this->acceleratorContract = Account::findByAddress(Account::ADDRESS_ACCELERATOR);
        $znnToken = znn_token();
        $qsrToken = qsr_token();

        $totalZnnUsed = $this->acceleratorContract
            ->sent_blocks()
            ->where('token_id', $znnToken->id)
            ->sum('amount');

        $totalQsrUsed = $this->acceleratorContract
            ->sent_blocks()
            ->where('token_id', $qsrToken->id)
            ->sum('amount');

        $fundingLabels = [
            'Remaining',
            'Used',
        ];

        $this->azFundingZnn = [
            'labels' => $fundingLabels,
            'data' => [
                (int) filter_var($this->acceleratorContract->display_znn_balance, FILTER_SANITIZE_NUMBER_INT),
                (int) filter_var($znnToken->getDisplayAmount($totalZnnUsed), FILTER_SANITIZE_NUMBER_INT),
            ],
        ];

        $this->azFundingQsr = [
            'labels' => $fundingLabels,
            'data' => [
                (int) filter_var($this->acceleratorContract->display_qsr_balance, FILTER_SANITIZE_NUMBER_INT),
                (int) filter_var($znnToken->getDisplayAmount($totalQsrUsed), FILTER_SANITIZE_NUMBER_INT),
            ],
        ];
    }

    private function loadProjectData()
    {
        $this->azProjectTotals = [
            'labels' => ['New', 'Accepted', 'Completed', 'Rejected'],
            'data' => [
                AcceleratorProject::isNew()->count(),
                AcceleratorProject::isAccepted()->count(),
                AcceleratorProject::isCompleted()->count(),
                AcceleratorProject::isRejected()->count(),
            ],
        ];
    }

    private function loadEngagementData()
    {
        $this->engagementData = Pillar::whereHas('az_votes')
            ->withCount('az_votes')
            ->orderBy($this->engagementSort, $this->engagementOrder)
            ->simplePaginate(10);
    }
}
