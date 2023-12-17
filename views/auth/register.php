<h1 class="nombre-pagina">Registrar</h1>
<p class="descripcion-pagina">LLena el siguiente formulario para crear una cuenta</p>

<?php
    include_once __DIR__ . '/../templates/alertas.php';
?>

<form class="formulario" method="POST" action="/register">
    <div class="campo">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre" value="<?php echo s($usuario->nombre); ?>">
    </div>

    <div class="campo">
        <label for="apellido">Apellido</label>
        <input type="text" id="apellido" name="apellido" placeholder="Apellido" value="<?php echo s($usuario->apellido); ?>">
    </div>

    <div class="campo">
        <label for="celular">Celular</label>
        <input type="tel" id="celular" name="celular" placeholder="(ej: 9XXXXXXXX)" value="<?php echo s($usuario->celular); ?>">
    </div>

    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Email" value="<?php echo s($usuario->email); ?>">
    </div>

    <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="La Contraseña debe tener mas de 6 caracteres">
    </div>

    <input type="submit" value="Crear Cuenta" class="boton">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesion</a>
    <a href="/forget">¿Olvidaste tu Contraseña?</a>
</div>