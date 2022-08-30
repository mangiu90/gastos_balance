<?php

namespace App\Orchid\Screens\Eventos;

use Orchid\Screen\TD;
use App\Models\Evento;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;

class EventosListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'eventos' => Evento::filters()->paginate(),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Eventos';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Crear Evento')
                ->modal('crearEventoModal')
                ->method('crearEvento')
                ->icon('add'),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('eventos', [
                TD::make('nombre'),
            ]),

            Layout::modal('crearEventoModal', [
                Layout::rows([
                    Input::make('evento.nombre')
                        ->required()
                        ->title('Nombre'),
                ]),
            ]),
        ];
    }

    public function crearEvento(Request $request): void
    {
        Evento::create($request->evento);
        
        Toast::info('Evento creado.');
    }
}
