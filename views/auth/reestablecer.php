<div class="contenedor reestablecer">
<?php
@include_once __DIR__ . '/../templates/nombre-sitio.php';
?>
    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca Tu Nuevo Password</p>
<?php 
@include_once __DIR__ . '/../templates/alertas.php';
?>
    <?php if($mostrar){?>
        <form class="formulario" method="POST">
            <div class="campo">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Tu Password">
            </div>
            <div class="campo">
                <label for="password2">Repetir Password</label>
                <input type="password" id="password2" name="password2" placeholder="Confirma Tu Password">
            </div>

            <input type="submit" class="boton" value="Guardar Contraseña">
        </form> 
        <?php };?>
        <div class="acciones">
            <a href="/crear">¿No tienes una cuenta? Obtener una</a>
            <a href="/olvide">¿Olvidaste tu Password?</a>
        </div>
    </div> <!--.contenedor-sm-->
</div>