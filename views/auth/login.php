<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesion con tus datos</p>

<?php
    include_once __DIR__ . '/../templates/alertas.php';
?>

<form class="formulario" method="POST" action="/">
    <div class="campo">
        <label for="email">Email</label>
        <input class="form-control" type="email" name="email" id="email" placeholder="Ingresa tu email">
    </div>
    <div class="campo">
        <label for="password">Contraseña</label>
        <input class="form-control" type="password" name="password" id="password" placeholder="Ingresa tu contraseña">
    </div>

    <input class="boton" type="submit" value="Iniciar sesion">       
</form>

<div class="acciones">
    <a href="/register">¿Aun no tienes una cuenta? Registrate</a>
    <a href="/forget">¿Olvidaste tu Contraseña?</a>
</div>