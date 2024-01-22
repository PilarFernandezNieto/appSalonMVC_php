<h1 class="nombre-pagina">Actualizar Cita</h1>
<formulario class="formulario">
    <!-- <?php //debuguear($servicios); ?> -->
    <input type="hidden" value="<?php echo $cita->id; ?>">
    <div class="campo">
        <label for="fecha">Fecha</label>
        <input type="date" id="fecha" name="fecha" value="<?php echo $cita->fecha; ?>">
    </div>
    <div class="campo">
        <label for="hora">Hora</label>
        <input type="time" id="hora" name="hora" value="<?php echo $cita->hora; ?>">
    </div>
    <div>
        <select name="servicios" id="servicios">
            <?php 
            foreach($servicios as $servicio) {
            ?>
            <option value="<?php $servicio->id; ?>"><?php echo $servicio->nombre; ?></option>
            <?php } ?>
        </select>
    </div>
</formulario>