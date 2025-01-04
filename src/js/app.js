const mobileMenuBtn = document.querySelector("#mobile-menu");
const sidebar = document.querySelector(".sidebar");
if (mobileMenuBtn) {
  mobileMenuBtn.addEventListener("click", function () {
    sidebar.classList.toggle("mostrar");

  });
}

const cerrarSidebar = document.querySelector("#sidebar-cerrar");

if (cerrarSidebar) {
  cerrarSidebar.addEventListener("click",function () {
    console.log('aqui');
    sidebar.classList.add("ocultar");
    setTimeout(() => {
        sidebar.classList.remove("mostrar");
        sidebar.classList.remove("ocultar");
    }, 1000);
  });
}
