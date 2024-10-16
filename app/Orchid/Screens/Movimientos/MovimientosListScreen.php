<?php

namespace App\Orchid\Screens\Movimientos;

use App\Models\User;
use Orchid\Screen\TD;
use App\Models\Evento;
use Orchid\Screen\Screen;
use App\Models\Movimiento;
use App\Orchid\Layouts\Movimientos\MovimientoFiltersLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;

class MovimientosListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        $filter = $request->get('filter', []);

        return [
            'filter' => $filter,
            'movimientos' => Movimiento::with('user', 'evento')
                ->when(isset($filter['name']), function ($query) use ($filter) {
                    return $query->whereRelation('user', 'name', 'like', '%' . $filter['name'] . '%');
                })
                ->when(isset($filter['evento']), function ($query) use ($filter) {
                    return $query->whereRelation('evento', 'nombre', 'like', '%' . $filter['evento'] . '%');
                })
                // ->filters(MovimientoFiltersLayout::class)
                ->defaultSort('fecha', 'desc')
                ->paginate(),
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
            ModalToggle::make('Añadir Movimiento')
                ->modal('crearMovimientoModal')
                ->modalTitle('Añadir Movimiento')
                ->method('crearMovimiento')
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
            // MovimientoFiltersLayout::class,

            Layout::rows([
                Group::make([
                    Input::make('filter.name')
                        ->placeholder('Usuario ...'),
                    Input::make('filter.evento')
                        ->placeholder('Evento ...'),
                ]),
                Group::make([
                    Button::make('Filtrar')
                        ->method('filtrar'),
                    Link::make('Limpiar')
                        ->route('platform.movimientos.list'),
                ]),
            ]),

            Layout::table('movimientos', [
                TD::make('user.name', 'Usuario'),
                TD::make('evento.nombre', 'Evento'),
                TD::make('fecha'),
                TD::make('detalle'),
                TD::make('tipo')
                    ->render(function ($row) {
                        return $row->tipo === Movimiento::INGRESO
                            ? '<span class="text-danger">' . $row->tipo . '</span>'
                            : '<span class="text-success">' . $row->tipo . '</span>';
                    }),
                TD::make('monto')
                    ->alignRight()
                    ->render(function ($row) {
                        return $row->tipo === Movimiento::INGRESO
                            ? '<span class="text-danger">' . number_format($row->monto, 2) . '</span>'
                            : '<span class="text-success">' . number_format($row->monto, 2) . '</span>';
                    }),

                TD::make()
                    ->alignRight()
                    ->render(function ($row) {
                        return Button::make()
                            ->icon('trash')
                            ->confirm('Estas seguro de eliminar este movimiento?')
                            ->method('eliminar', ['movimiento_id' => $row->id]);
                    }),
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
                    Input::make('movimiento.detalle')
                        // ->required()
                        ->title('Detalle'),
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

        $user = User::findOrFail($request->movimiento['user_id']);
        $evento = $user->eventoPropio($request->movimiento['evento_id']);

        if (isset($evento)) {
            Movimiento::create($data);

            Toast::info('Movimiento creado.');
        } else {
            Toast::error('El usario no pertenece a este evento.');
        }
    }

    public function eliminar(Request $request): void
    {
        Movimiento::findOrFail($request->movimiento_id)->delete();

        Toast::info('Movimiento eliminado.');
    }

    public function filtrar(Request $request)
    {
        return redirect()->route('platform.movimientos.list', ['filter' => $request->filter]);
    }
}
