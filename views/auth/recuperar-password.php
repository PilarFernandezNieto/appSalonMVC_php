<h1 class="nombre-pagina">Recuperar Passowrd</h1>
<p class="descripcion-pagina">Inserta una nueva contraseña</p>
<?php include_once __DIR__ . "/../templates/alertas.php"; ?>

<?php if($error) return ?>
<form method="POST" class="formulario">
    <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Nueva Contraseña">
    </div>
    <input type="submit" class="boton" value="Guardar nueva contraseña">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes cuenta? Iniciar Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes cuenta? Obtener una</a>
</div>