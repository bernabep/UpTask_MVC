<?php @include_once __DIR__ . '/header-dashboard.php';?>
<div class="contenedor-sm">
    <form class="formulario" method="POST">
        <div class="campo">
            <label for="nombre">Nombre:</label>
            <input type="nombre"
            id="nombre"
            name="nombre"
            placeholder="Introduce el Nuevo Nombre"
            value="<?php echo $usuario->nombre; ?>"
            >
        </div>
        <div class="campo">
            <label for="email">Email</label>
            <input type="email"
            id="email"
            name="email"
            placeholder="Introduce el Nuevo Email"
            value="<?php echo $usuario->email; ?>"
            >
        </div>
        <input type="submit" value="Guardar Cambios">
    </form>
</div>


<?php @include_once __DIR__ . '/footer-dashboard.php';?>