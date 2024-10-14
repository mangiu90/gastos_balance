
<h1>Transferencias</h1>
@if (isset($transferencias))
@foreach($transferencias as $transferencia)
<p>{{$transferencia['deudor_nombre'] . " debe transferir " . $transferencia['monto'] . " a " . $transferencia['acreedor_nombre']}}</p>
@endforeach
@endif