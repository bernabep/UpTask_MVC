<aside class="sidebar">
    <div class="sidebar-header">
    <h2>UpTask</h2>

    <img id="sidebar-cerrar" src="build/img/cerrar.svg" alt="imagen cerrar"/>
    </div>

    <nav class="sidebar-nav">
        <a class="<?php echo ($titulo === 'Proyectos') ? 'activo' : '';?>" href="/dashboard">Proyectos</a>
        <a class="<?php echo ($titulo === 'Crear Proyecto') ? 'activo' : '';?>" href="/crear-proyecto">Crear Proyecto</a>
        <a class="<?php echo ($titulo === 'Perfil') ? 'activo' : '';?>" href="/perfil">Perfil</a>
    </nav>
    <div class="cerrar-sesion-mobile">
            <a class="cerrar-sesion" href="/logout">Cerrar Sesión</a>
    </div>

</aside>