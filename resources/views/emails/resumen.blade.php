@component('mail::message')
<style>
    .resumen table,
    .resumen th,
    .resumen td {
        border: 1px solid;
    }

</style>
@foreach ($eventos as $evento)
# {{ $evento['nombre'] }}

<p>Gastos Totales: {{ $evento['gastos'] }}</p>
<p>Gastos por Usuario: {{ $evento['gastos_usuario'] }}</p>

<table class="resumen">
    <tr>
        <td>Usuario</td>
        <td>Gasto</td>
        <td>Balance</td>
    </tr>
    @foreach ($evento['users'] as $user)
    <tr>
        <td>{{ $user['name'] }}</td>
        <td>{{ $user['gastos'] }}</td>
        <td>{{ $user['balance'] }}</td>
    </tr>
    @endforeach

</table>

@endforeach

@endcomponent
