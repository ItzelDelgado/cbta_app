<?php

namespace App\Livewire\Oncologicos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Oncologicos\MedicinesCatalog;

class MedicinesCatalogTable extends Component
{
    use WithPagination;

    public $buscar = '';
    public $search = '';

    public $sortField = 'id';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function aplicarBusqueda()
    {
        $this->search = trim($this->buscar);
        $this->resetPage();
    }

    public function render()
    {
        $query = MedicinesCatalog::query()
            ->where('state', true);

        if ($this->search !== '') {
            $s = $this->search;
            $isNumeric = ctype_digit($s);

            $query->where(function ($q) use ($s, $isNumeric) {
                if ($isNumeric) {
                    $q->orWhere('id', $s);
                }

                $q->orWhere('denominacion', 'like', "%{$s}%");
            });
        }

        $medicamentos = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(50);

        return view('livewire.oncologicos.medicines-catalog-table', compact('medicamentos'));
    }
}
