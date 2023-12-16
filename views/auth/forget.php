<h1 class="nombre-pagina">Recuperar Contraseña</h1>
<p class="descripcion-pagina">Reestablece tu Contraseña escribiendo tu email a continuacion</p>

<?php
    include_once __DIR__ . '/../templates/alertas.php';
?>

<form action="/forget" class="formulario" method="POST">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Email">
    </div>

    <input type="submit" value="Recuperar Contraseña" class="boton">    
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesion</a>
    <a href="/register">¿Aun no tienes una cuenta? Registrate</a>
</div>