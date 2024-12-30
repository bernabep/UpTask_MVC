
(function () {
  let tareas = [];
  obtenerTareas();

  //Boton añadir tarea
  const nuevaTareaBtn = document.querySelector("#agregar-tarea");
  nuevaTareaBtn.addEventListener("click", mostrarFormulario);

  function mostrarFormulario() {
    const modal = document.createElement("DIV");
    modal.classList.add("modal");
    modal.innerHTML = `
    <form class='formulario nueva-tarea'>
        <legend>Añade una nueva tarea</legend>
        <div class="campo">
            <label for="tarea">Tarea</label>
            <input 
            type="text"
            name="tarea"
            id="tarea"
            placeholder="Añadir Tarea al Proyecto Actual"
            />
        </div>
        <div class="opciones">
            <input type="submit" class="submit-nueva-tarea" value="Añadir Tarea"/>
            <button type="button" class="cerrar-modal">Cancelar</button>
        </div>
    </form>    
    `;
    setTimeout(() => {
      const formulario = document.querySelector(".formulario");
      formulario.classList.add("animar");
    }, 0);

    modal.addEventListener("click", function (e) {
      e.preventDefault();
      if (e.target.classList.contains("cerrar-modal")) {
        const formulario = document.querySelector(".formulario");
        formulario.classList.add("cerrar");
        setTimeout(() => {
          modal.remove();
        }, 500);
      }
      if (e.target.classList.contains("submit-nueva-tarea")) {
        submitFormularioNuevaTarea();
      }
    });

    document.querySelector(".dashboard").appendChild(modal);
  }

  function mostrarTareas() {
    limpiarTareas();
    const contenedorTareas = document.querySelector("#listado-tareas");
    if (tareas.length === 0) {
      const textoNoTareas = document.createElement("LI");
      textoNoTareas.textContent = "No Hay Tareas";
      textoNoTareas.classList.add("no-tareas");
      contenedorTareas.appendChild(textoNoTareas);
    } else {
      const estados = {
        0: "Pendiente",
        1: "Completa",
      };
      tareas.forEach((tarea) => {
        const contenedorTarea = document.createElement("LI");
        contenedorTarea.dataset.tareaId = tarea.id;
        contenedorTarea.classList.add("tarea");

        const nombreTarea = document.createElement("P");
        nombreTarea.textContent = tarea.nombre;

        const opcionesDiv = document.createElement("DIV");
        opcionesDiv.classList.add("opciones");

        //Botones
        const btnEstadoTarea = document.createElement("BUTTON");
        btnEstadoTarea.classList.add(
          "tarea-estado",
          estados[tarea.estado].toLowerCase()
        );
        btnEstadoTarea.textContent = estados[tarea.estado];
        btnEstadoTarea.dataset.estadoTarea = tarea.estado;
        btnEstadoTarea.ondblclick = function () {
          cambiarEstadoTarea({ ...tarea });
        };

        btnEliminarTarea = document.createElement("BUTTON");
        btnEliminarTarea.classList.add("eliminar-tarea");
        btnEliminarTarea.dataset.idTarea = tarea.id;
        btnEliminarTarea.textContent = "Eliminar";
        btnEliminarTarea.ondblclick = function(){
          confirmarEliminarTarea({...tarea});
        } 

        opcionesDiv.appendChild(btnEstadoTarea);
        opcionesDiv.appendChild(btnEliminarTarea);

        contenedorTarea.appendChild(nombreTarea);
        contenedorTarea.appendChild(opcionesDiv);
        contenedorTareas.appendChild(contenedorTarea);
      });
    }
  }

  function submitFormularioNuevaTarea() {
    const tarea = document.querySelector("#tarea").value.trim();
    limpiarAlertas();
    if (tarea == "") {
      mostrarAlerta(
        "Es obligatorio Indicar la Tarea",
        "error",
        document.querySelector(".formulario legend")
      );
      return;
    }

    agregarTarea(tarea);
    // mostrarAlerta('Tarea añadida correctamente','exito',document.querySelector('.formulario legend'))
  }

  function mostrarAlerta(mensaje, tipo, referencia) {
    const alerta = document.createElement("DIV");
    alerta.classList.add("alerta", tipo);
    alerta.textContent = mensaje;

    referencia.parentElement.insertBefore(
      alerta,
      referencia.nextElementSibling
    );

    setTimeout(() => {
      limpiarAlertas();
    }, 5000);
  }

  function limpiarAlertas() {
    const alertas = document.querySelectorAll(".alerta");
    alertas.forEach(function (elemento, indice, array) {
      elemento.remove();
    });
  }

  function obtenerProyecto() {
    const proyectoParams = new URLSearchParams(window.location.search);
    const proyecto = Object.fromEntries(proyectoParams.entries());
    return proyecto.id;
  }

  function limpiarTareas() {
    const listadoTareas = document.querySelector(".listado-tareas");
    while (listadoTareas.firstChild) {
      listadoTareas.removeChild(listadoTareas.firstChild);
    }
  }

  function cambiarEstadoTarea(tarea) {
    // console.log(tarea);

    tarea.estado =
      tarea.estado === "0" ? (tarea.estado = "1") : (tarea.estado = "0");

    //   console.log(tarea);
    //   console.log(tareas);
    actualizarTarea(tarea);
    
  }

  async function actualizarTarea(tarea) {
    const { id, nombre, estado, proyectoId } = tarea;
    const datos = new FormData();
    datos.append("id", id);
    datos.append("nombre", nombre);
    datos.append("estado", estado);
    datos.append("proyectoId", proyectoId);
    datos.append("url", obtenerProyecto());
    try {
      const url = "http://localhost:3000/api/tarea/actualizar";
      const respuesta = await fetch(url, {
        method: "POST",
        body: datos,
      });
      const resultado = await respuesta.json();
      if (resultado.respuesta.tipo === "exito") {
        mostrarAlerta(
          resultado.respuesta.mensaje,
          resultado.respuesta.tipo,
          document.querySelector(".contenedor-nueva-tarea")
        );

        tareas = tareas.map(tareaMemoria=>{
          if(tareaMemoria.id === id){
            tareaMemoria.estado = estado;
          }
          return tareaMemoria;
        });
        mostrarTareas();
      }
    } catch (error) {
      console.log(error);
    }
  }
  function confirmarEliminarTarea(tarea){
    Swal.fire({
      title: "Seguro que quieres eliminar la Tarea?",
      showDenyButton: true,
      showCancelButton: false,
      confirmButtonText: "Eliminar Tarea",
      denyButtonText: `Cancelar`
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        eliminarTarea(tarea);
        Swal.fire("Tarea Eliminada!", "", "success");
      } else if (result.isDenied) {
        Swal.fire("No se ha eliminado la tarea", "", "info");
      }
    });
    
  }

  async function eliminarTarea(tarea){
    const { id, nombre, estado, proyectoId } = tarea;
    const datos = new FormData();
    datos.append("id", id);
    datos.append("nombre", nombre);
    datos.append("estado", estado);
    datos.append("proyectoId", proyectoId);
    datos.append("url", obtenerProyecto());

    try {
      const url = 'http://localhost:3000/api/tarea/eliminar';
      const respuesta = await fetch(url,{
        method: "POST",
        body: datos
      });
      const resultado = await respuesta.json()
      if(resultado.respuesta.tipo === 'exito'){
        mostrarAlerta(
          resultado.respuesta.mensaje,
          resultado.respuesta.tipo,
          document.querySelector(".contenedor-nueva-tarea")
        );

        tareas = tareas.map(tareaMemoria=>{
          if(tareaMemoria.id !== id){
            return tareaMemoria;
          }
        });
        mostrarTareas();
      }

    } catch (error) {
      
    }
  }

  async function agregarTarea(tarea) {
    const datos = new FormData();

    datos.append("nombre", tarea);
    datos.append("proyectoId", obtenerProyecto());

    try {
      const url = "http://localhost:3000/api/tarea";
      const respuesta = await fetch(url, {
        method: "POST",
        body: datos,
      });
      const resultado = await respuesta.json();
      mostrarAlerta(
        resultado.mensaje,
        resultado.tipo,
        document.querySelector(".formulario legend")
      );
      if (resultado.tipo === "exito") {
        setTimeout(() => {
          const modal = document.querySelector(".modal");
          modal.remove();
        }, 3000);

        //Crear objeto de tarea
        const tareaObj = {
          id: String(resultado.id),
          nombre: tarea,
          estado: "0",
          proyectoId: resultado.proyectoId,
        };

        tareas = [...tareas, tareaObj];
        mostrarTareas();
      }
    } catch (error) {
      console.log(error);
    }
  }

  async function obtenerTareas() {
    try {
      const id = obtenerProyecto();
      const url = `http://localhost:3000/api/tareas?id=${id}`;
      const respuesta = await fetch(url);
      const resultado = await respuesta.json();
      tareas = resultado.tareas;
      mostrarTareas();
    } catch (error) {
      console.log(error);
    }
  }
})();
