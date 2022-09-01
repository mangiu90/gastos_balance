<?php

namespace App\Orchid\Screens\Eventos;

use Orchid\Screen\TD;
use App\Models\Evento;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
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
                ->icon('plus'),
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
                TD::make('nombre')
                    ->cantHide()
                    ->sort()
                    ->filter(TD::FILTER_TEXT)
                    ->render(function ($row) {
                        return Link::make($row->nombre)
                            ->route('platform.eventos.show', $row);
                    }),
                TD::make('Gastos totales')
                    ->alignRight()
                    ->render(function ($row) {
                        return number_format($row->gastos(), 2);
                    }),
                TD::make('Gastos por usuario')
                    ->alignRight()
                    ->render(function ($row) {
                        return number_format($row->gastosPorUsuario(), 2);
                    }),


                TD::make()
                    ->alignRight()
                    ->cantHide()
                    ->render(function ($row) {
                        return Button::make()
                            ->icon('trash')
                            ->confirm('Estas seguro de eliminar este evento?')
                            ->method('eliminar', ['evento_id' => $row->id]);
                    }),
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

    public function eliminar(Request $request): void
    {
        Evento::findOrFail($request->evento_id)->delete();

        Toast::info('Evento eliminado.');
    }
}
