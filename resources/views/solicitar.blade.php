<form id="datos" action="{{ url('solicitar') }}" method="POST">
{{ csrf_field() }}
	Comuna Origen
	<select id="origen" name="origen">
		<?php
		foreach ($comunas as $comuna) {
		?>
		<option value="{{$comuna['codigo']}}">{{$comuna['nombre']}}</option>
		<?php 
		}?>
	</select>
	<br>
	Comuna destino
		<select id="destino" name="destino">
		<?php
		foreach ($comunas as $comuna) {
		?>
		<option value="{{$comuna['codigo']}}">{{$comuna['nombre']}}</option>
		<?php 
		}?>
	</select>
	<br>
	kilos<input type="text" name="kilos" id="kilos" value="">
	<br>
	Largo<input type="text" name="largo" id="largo" value="">
	<br>
	alto<input type="text" name="alto" id="alto" value="">
	<br>
	Ancho(cm)<input type="text" name="ancho" id="ancho" value="">
	<br>
	<button type="submit" id="obtenervalores">Enviar</button>
	<br>

	<div id="response-container"></div>
</form>