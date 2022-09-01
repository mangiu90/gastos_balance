<?php

namespace App\Orchid\Layouts\Movimientos;

use App\Orchid\Filters\UserNameFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class MovimientoFiltersLayout extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            UserNameFilter::class,
        ];
    }
}
