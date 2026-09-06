<?php

namespace App\Livewire;

use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseIndex extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatedSearch($value)
    {
        $this->search = preg_replace('/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\-\.\_\@]/u', '', $value);
        $this->resetPage();
    }

    #[On('purchase-created')]
    #[On('purchase-cancelled')]
    #[On('echo:purchases,PurchaseCreated')]
    #[On('echo:purchases,PurchaseCancelled')]
    public function refreshPurchases()
    {
        // El re-render del componente actualizará automáticamente los listados y métricas
    }

    public function getTotalPurchases()
    {
        return Purchase::count();
    }

    public function getTotalInvestmentUsd()
    {
        // Los totales de compra se almacenan en USD.
        return Purchase::sum('total');
    }

    public function getTotalInvestmentBs()
    {
        // Equivalente en Bs usando la tasa congelada en cada compra.
        return Purchase::sum(DB::raw('total * COALESCE(exchange_rate, 0)'));
    }

    public function render()
    {
        $searchTerm = '%'.trim($this->search).'%';

        $purchases = Purchase::with(['supplier', 'user', 'details.product', 'details.bulk'])
            ->when($this->search, function ($query) use ($searchTerm) {
                $query->where('purchase_code', 'like', $searchTerm)
                    ->orWhereHas('supplier', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', $searchTerm)
                            ->orWhere('rif', 'like', $searchTerm);
                    });
            })
            ->orderBy('purchased_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.purchase-index', [
            'purchases' => $purchases,
            'totalPurchases' => $this->getTotalPurchases(),
            'totalInvestmentBs' => $this->getTotalInvestmentBs(),
            'totalInvestmentUsd' => $this->getTotalInvestmentUsd(),
        ]);
    }
}
