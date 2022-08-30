<?php

namespace App\Orchid\Screens\Movimientos;

use App\Models\Evento;
use Orchid\Screen\TD;
use Orchid\Screen\Screen;
use App\Models\Movimiento;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Toast;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;

class MovimientosListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'movimientos' => Movimiento::with('user', 'evento')->filters()->paginate(),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Movimientos';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Crear Movimiento')
                ->modal('crearMovimientoModal')
                ->method('crearMovimiento')
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
            Layout::table('movimientos', [
                TD::make('user.name', 'Usuario'),
                TD::make('evento.nombre', 'Evento'),
                TD::make('fecha'),
                TD::make('monto'),
                TD::make('tipo'),
            ]),

            Layout::modal('crearMovimientoModal', [
                Layout::rows([
                    Relation::make('movimiento.user_id')
                        ->fromModel(User::class, 'name')
                        ->required()
                        ->title('Usuario'),
                    Relation::make('movimiento.evento_id')
                        ->fromModel(Evento::class, 'nombre')
                        ->required()
                        ->title('Evento'),
                    Input::make('movimiento.monto')
                        ->required()
                        ->mask([
                            'alias' => 'currency',
                            'prefix' => '',
                            'groupSeparator' => '',
                            'digitsOptional' => true,
                        ])
                        ->title('Monto'),
                    Select::make('movimiento.tipo')
                        ->options(Movimiento::TIPO_OPTIONS)
                        ->required()
                        ->title('Tipo')
                ]),
            ]),
        ];
    }

    public function crearMovimiento(Request $request): void
    {
        $data = $request->movimiento;
        $data['fecha'] = now();

        Movimiento::create($data);

        Toast::info('Movimiento creado.');
    }
}
