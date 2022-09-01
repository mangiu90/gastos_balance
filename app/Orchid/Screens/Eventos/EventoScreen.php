<?php

namespace App\Orchid\Screens\Eventos;

use App\Models\User;
use Orchid\Screen\TD;
use App\Models\Evento;
use App\Models\Movimiento;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;

class EventoScreen extends Screen
{
    /**
     * @var Evento
     */
    public $evento;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Evento $evento): iterable
    {
        return [
            'evento' => $evento,
            'users' => $evento->users()->filters()->paginate(),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->evento->nombre;
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return 'Gastos totales: ' . number_format($this->evento->gastos(), 2) . ' | Gastos por usuario: ' . number_format($this->evento->gastosPorUsuario(), 2);
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Añadir Usuario')
                ->modal('addUserModal')
                ->method('addUser')
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
            Layout::table('users', [
                TD::make('name'),
                TD::make('Gastos')
                    ->alignRight()
                    ->render(function ($row) {
                        return number_format($row->gastosPorEvento($this->evento->id), 2);
                    }),
                TD::make('Balance')
                    ->alignRight()
                    ->render(function ($row) {
                        return number_format($row->balancePorEvento($this->evento->id), 2);
                    }),
                TD::make()
                    ->alignRight()
                    ->cantHide()
                    ->render(function ($row) {
                        return Button::make()
                            ->icon('trash')
                            ->confirm('Estas seguro de eliminar este usuario? Se borraran todos los movimientos de este usuario en este evento.')
                            ->method('eliminar', ['user_id' => $row->id]);
                    }),
            ]),

            Layout::modal('addUserModal', [
                Layout::rows([
                    Relation::make('user_id')
                        ->fromModel(User::class, 'name')
                        ->required()
                        ->title('Usuario'),
                ]),
            ]),
        ];
    }

    public function addUser(Evento $evento, Request $request)
    {
        $evento->users()->syncWithoutDetaching($request->user_id);
    }

    public function eliminar(Evento $evento, Request $request): void
    {
        $evento->movimientos()->where('user_id', $request->user_id)->delete();
        $evento->users()->detach($request->user_id);
    }
}
