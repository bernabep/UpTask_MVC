(function () {
  let tareas = [];
  let filtradas = [];
  obtenerTareas();

  //Boton añadir tarea
  const nuevaTareaBtn = document.querySelector("#agregar-tarea");
  const filtros = document.querySelectorAll('#filtros input[type="radio"]');
  filtros.forEach((radio) => {
    radio.addEventListener("input", filtrarTareas);
  });

  nuevaTareaBtn.addEventListener("click", function () {
    mostrarFormulario();
  });

  function filtrarTareas(e) {
    const filtro = e.target.value;
    console.log(tareas);
    console.log(filtro);
    if (filtro !== "") {
      filtradas = tareas.filter(
        (tareaMemoria) => tareaMemoria.estado == filtro
      );
    } else {
      filtradas = [];
    }
    mostrarTareas();
  }
  function mostrarFormulario(editar = false, tarea = {}) {
    const modal = document.createElement("DIV");
    modal.classList.add("modal");
    modal.innerHTML = `
    <form class='formulario nueva-tarea'>
        <legend>${editar ? "Editar la Tarea" : "Añade una nueva tarea"}</legend>
        <div class="campo">
            <label for="tarea">Tarea</label>
            <input 
            type="text"
            name="tarea"
            id="tarea"
            placeholder="${
              tarea.nombre
                ? "Edita la Tarea"
                : "Añadir Tarea al Proyecto Actual"
            }"
            value="${editar ? tarea.nombre : ""}"
            />
        </div>
        <div class="opciones">
            <input type="submit" class="submit-nueva-tarea" value="${
              editar ? "Editar Tarea" : "Añadir Tarea"
            }"/>
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
        const nombreTarea = document.querySelector("#tarea").value.trim();
        limpiarAlertas();
        if (nombreTarea == "") {
          mostrarAlerta(
            "Es obligatorio Indicar la Tarea",
            "error",
            document.querySelector(".formulario legend")
          );
          return;
        }
        if (editar) {
          tarea.nombre = nombreTarea;
          actualizarTarea(tarea);
        } else {
          agregarTarea(nombreTarea);
        }
        setTimeout(() => {
          modal.remove();
        }, 500);
      }
    });

    document.querySelector(".dashboard").appendChild(modal);
  }

  function mostrarTareas() {
    limpiarTareas();
    totalTareasCompletadas();
    totalTareasPendientes();
    const arrayTareas = filtradas.length ? filtradas : tareas;
    const contenedorTareas = document.querySelector("#listado-tareas");
    if (arrayTareas.length === 0) {
      const textoNoTareas = document.createElement("LI");
      textoNoTareas.textContent = "No Hay Tareas";
      textoNoTareas.classList.add("no-tareas");
      contenedorTareas.appendChild(textoNoTareas);
    } else {
      const estados = {
        0: "Pendiente",
        1: "Completa",
      };
      arrayTareas.forEach((tarea) => {
        const contenedorTarea = document.createElement("LI");
        contenedorTarea.dataset.tareaId = tarea.id;
        contenedorTarea.classList.add("tarea");

        const nombreTarea = document.createElement("P");
        nombreTarea.textContent = tarea.nombre;
        nombreTarea.ondblclick = function () {
          mostrarFormulario((editar = true), (tarea = { ...tarea }));
        };

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
        btnEliminarTarea.ondblclick = function () {
          confirmarEliminarTarea({ ...tarea });
        };

        opcionesDiv.appendChild(btnEstadoTarea);
        opcionesDiv.appendChild(btnEliminarTarea);

        contenedorTarea.appendChild(nombreTarea);
        contenedorTarea.appendChild(opcionesDiv);
        contenedorTareas.appendChild(contenedorTarea);
      });
    }
  }

  function totalTareasCompletadas() {
    const tareasCompletadas = tareas.filter(tarea=>tarea.estado==="1");
    const radioCompletadas = document.querySelector('#completadas')
    if(tareasCompletadas.length === 0){
      radioCompletadas.disabled = true;
    }else{
      radioCompletadas.disabled = false;
    }
  }
  function totalTareasPendientes() {
    const tareasPendientes = tareas.filter(tarea=>tarea.estado==="0");
    const radioPendientes = document.querySelector('#pendientes')
    if(tareasPendientes.length === 0){
      radioPendientes.disabled = true;
    }else{
      radioPendientes.disabled = false;
    }
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
    tarea.estado =
      tarea.estado === "0" ? (tarea.estado = "1") : (tarea.estado = "0");
    actualizarTarea(tarea);
  }

  function confirmarEliminarTarea(tarea) {
    Swal.fire({
      title: "Seguro que quieres eliminar la Tarea?",
      showDenyButton: true,
      showCancelButton: false,
      confirmButtonText: "Eliminar Tarea",
      denyButtonText: `Cancelar`,
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        eliminarTarea(tarea);
      } else if (result.isDenied) {
        Swal.fire("No se ha eliminado la tarea", "", "info");
      }
    });
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
        Swal.fire({
          title: resultado.respuesta.mensaje,
          icon: "success",
          draggable: true,
        });

        tareas = tareas.map((tareaMemoria) => {
          if (tareaMemoria.id === id) {
            tareaMemoria.estado = estado;
            tareaMemoria.nombre = nombre;
          }
          return tareaMemoria;
        });
        mostrarTareas();
      }
    } catch (error) {
      console.log(error);
    }
  }

  async function eliminarTarea(tarea) {
    const { id, nombre, estado, proyectoId } = tarea;
    const datos = new FormData();
    datos.append("id", id);
    datos.append("nombre", nombre);
    datos.append("estado", estado);
    datos.append("proyectoId", proyectoId);
    datos.append("url", obtenerProyecto());

    try {
      const url = "http://localhost:3000/api/tarea/eliminar";
      const respuesta = await fetch(url, {
        method: "POST",
        body: datos,
      });
      const resultado = await respuesta.json();
      if (resultado.respuesta.tipo === "exito") {
        // mostrarAlerta(
        //   resultado.respuesta.mensaje,
        //   resultado.respuesta.tipo,
        //   document.querySelector(".contenedor-nueva-tarea")
        // );
        Swal.fire("Tarea Eliminada!", "", "success");
        tareas = tareas.filter((tareaMemoria) => tareaMemoria.id !== id);

        mostrarTareas();
      }
    } catch (error) {
      console.log(error);
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
      // mostrarAlerta(
      //   resultado.mensaje,
      //   resultado.tipo,
      //   document.querySelector(".formulario legend")
      // );
      Swal.fire({
        title: resultado.mensaje,
        icon: "success",
        draggable: true,
      });
      if (resultado.tipo === "exito") {
        setTimeout(() => {
          const modal = document.querySelector(".modal");
          // modal.remove();
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
