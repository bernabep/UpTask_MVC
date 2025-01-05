<?php @include_once __DIR__ . '/header-dashboard.php'; ?>
<div class="contenedor-sm">
    <?php @include_once __DIR__ . '/../templates/alertas.php' ?>
    <a href="/perfil" class="enlace">Volver al Perfil</a>
    <form class="formulario" method="POST" action="/cambiar-password">
        <div class="campo">
            <label for="password_actual">Password Actual:</label>
            <input type="password"
                id="password"
                name="password_actual"
                placeholder="Tu Password Actual"s
                value="">
        </div>
        <div class="campo">
            <label for="password_nuevo">Password Nuevo"</label>
            <input type="password"
                id="password"
                name="password_nuevo"
                placeholder="Introduce tu Nuevo password"
                value="">
        </div>
        <input type="submit" value="Guardar Cambios">
    </form>
</div>


<?php @include_once __DIR__ . '/footer-dashboard.php'; ?>