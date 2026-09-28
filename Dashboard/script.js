// Datos de la tabla
let dataTable
let dataTableIsInitialized=false;

const dataTableOptions={
    // scrollX:"2000px",
    columnDefs:[
        {className:"centered", targets: [1,2,3,4,5]},
        {searcheable:false, targets:[2,3,4,5]},
    {orderable: false, targets:[5]},
    {with: "10%", targets:[0]}

],
    pageLength: 4,
    destroy: true,
    language: {
        lengthMenu: "Mostrar _MENU_ registros por página",
        zeroRecords: "Ningún usuario encontrado",
        info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
        infoEmpty: "Ningún usuario encontrado",
        infoFiltered: "(filtrados desde _MAX_ registros totales)",
        search: "Buscar:",
        loadingRecords: "Cargando...",
        paginate: {
            first: "Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior"
        }
    }
};

const initDataTable=async()=>{
if (dataTableIsInitialized){
    dataTable.destroy();
}

await listUsr();

dataTable=$("#datatable_usuarios").DataTable(dataTableOptions);

dataTableIsInitialized = true;
}

const listUsr = async () => {
    const tableBodyUsuarios = document.getElementById("tableBody_usuarios");

    if (!tableBodyUsuarios) {
        console.warn("No existe #tableBody_usuarios en el DOM.");
        return;
    }

    try {
        const response = await fetch("https://jsonplaceholder.typicode.com/users");
        if (!response.ok) throw new Error("No se pudo obtener los usuarios.");

        const usuarios = await response.json();

        tableBodyUsuarios.innerHTML = usuarios.map((user, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${user.address.city}</td>
                <td>${user.company.name} </td>
                <td>
                <button class="btn btn-sm btn-primary"> <span class="material-symbols-rounded">
edit_square
</span></button>
                <button class="btn btn-sm btn-danger"> <span class="material-symbols-rounded">
delete_forever
</span> </button>
                </td>
edit_square
</span> <span class="material-symbols-rounded" style="color: red">
delete_forever
</span></td>
                
            </tr>
        `).join("");

    } catch (error) {
        console.error(error);
        alert(error.message || error);
    }
};

window.addEventListener("load", async () => {
    await initDataTable();
});

// Despliegue del sidebar y cambio de tema
const menuBtn = document.querySelector("#menu_bar");
const closeBtn = document.querySelector("#close_btn");
const sideMenu = document.querySelector("aside");
const themeToggler = document.querySelector(".theme-toggler");

if (menuBtn) {
    menuBtn.addEventListener("click", () => {
        if (window.innerWidth <= 786) {
            sideMenu.style.display = "block";
            sideMenu.classList.add("mobile-open");
        }
    });
}

if (closeBtn) {
    closeBtn.addEventListener("click", () => {
        sideMenu.style.display = "none";
        sideMenu.classList.remove("mobile-open");
    });
}

if (themeToggler) {
    const themeIcons = themeToggler.querySelectorAll("span");

    themeToggler.addEventListener("click", () => {
        document.body.classList.toggle("dark-theme-variables");
        themeIcons.forEach(icon => icon.classList.toggle("active"));
    });
}

// Navegación del sidebar
const navLinks = document.querySelectorAll("#sidebar a[data-section]");
const sidebarLinks = document.querySelectorAll("#sidebar > a[data-section]");
const userDropdownToggle = document.querySelector(".usuarios-toggle");
const userDropdownLinks = document.querySelectorAll(".usuarios-dropdown .dropdown-item[data-section]");
const mainSections = document.querySelectorAll("main .main-section");

navLinks.forEach(link => {
    link.addEventListener("click", (e) => {
        e.preventDefault();

        const targetId = link.dataset.section;

        if (targetId === "logout") {
            return;
        }

        sidebarLinks.forEach(sidebarLink => {
            sidebarLink.classList.toggle("active", sidebarLink.dataset.section === targetId);
        });

        userDropdownLinks.forEach(dropdownLink => {
            dropdownLink.classList.toggle("active", dropdownLink.dataset.section === targetId);
        });

        if (userDropdownToggle) {
            userDropdownToggle.classList.toggle("active", ["clientes", "empleados"].includes(targetId));
        }

        mainSections.forEach(section => {
            section.classList.toggle("active", section.id === targetId);
        });

        if (window.innerWidth <= 786) {
            sideMenu.style.display = "none";
            sideMenu.classList.remove("mobile-open");
        }
    });
});

window.addEventListener("resize", () => {
    if (window.innerWidth > 786) {
        sideMenu.style.display = "";
        sideMenu.classList.remove("mobile-open");
    }
});