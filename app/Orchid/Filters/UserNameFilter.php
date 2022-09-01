<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;

class UserNameFilter extends Filter
{
    /**
     * @return string
     */
    public function name(): string
    {
        return __('User');
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['user'];
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        return $builder->whereHas('user', function (Builder $query) {
            $query->where('name', $this->request->get('user'));
        });
    }

    /**
     * @return Field[]
     */
    public function display(): array
    {
        return [
            Select::make('user')
                ->fromModel(User::class, 'name')
                ->empty()
                ->value($this->request->get('user'))
                ->title(__('Users')),
        ];
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->name() . ': ' . User::find($this->request->get('user'))->name;
    }
}
