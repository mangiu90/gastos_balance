<?php

namespace App\Console;

use App\Mail\Resumen;
use App\Models\Evento;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $headerData = ['USUARIO', 'EVENTO', 'FECHA', 'MONTO', 'TIPO', 'DETALLE'];
            $arrayData = [];
            Movimiento::with(['user', 'evento'])->each(function ($mov) use (&$arrayData) {
                $arrayData[] = [
                    $mov->user->name,
                    $mov->evento->nombre,
                    $mov->fecha,
                    $mov->monto,
                    $mov->tipo,
                    $mov->detalle,
                ];
            });
    
            $eventos = [];
    
            Evento::all()->map(function ($evento) use (&$eventos) {
                $ev['nombre'] = $evento->nombre;
                $ev['gastos'] = number_format((float)$evento->gastos(), 2);
                $ev['gastos_usuario'] = number_format((float)$evento->gastosPorUsuario(), 2);
                $ev['users'] = $evento->users->transform(function ($user) use ($evento) {
                    return [
                        'name' => $user->name,
                        'gastos' => number_format((float)$user->gastosPorEvento($evento->id), 2),
                        'balance' => number_format((float)$user->balancePorEvento($evento->id), 2),
                    ];
                });
    
                $eventos[] = $ev;
            });
    
            Mail::to('giudicimanuel@gmail.com')->send(new Resumen($eventos, $headerData, $arrayData));
        })->twiceDaily(1, 13);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
