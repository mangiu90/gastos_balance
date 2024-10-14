<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Evento;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;

class PlatformScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        // dd(request()->all());
        $evento_id = request()->get('evento_id', 0);

        $evento = Evento::find($evento_id);

        if ($evento) {
            // dd($evento->minimizarTransferencias());

            return [
                'transferencias' => $evento->minimizarTransferencias(),
            ];
        }
        return [

        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Get Started';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Welcome to your Orchid application.';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Relation::make('evento_id')
                    ->fromModel(Evento::class, 'nombre')
                    ->required()
                    ->title('Evento'),

                Button::make('Generar')
                    ->method('generar'),
            ]),

            Layout::view('transferencias'),
        ];
    }

    public function generar()
    {
        return redirect()->route('platform.main', ['evento_id' => request()->get('evento_id')]);
    }
}
