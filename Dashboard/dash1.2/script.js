// Datos de la tabla
let dataTable
let petsDataTable
let employeesDataTable
let productsDataTable
let providersDataTable
let appointmentsDataTable
let dataTableIsInitialized=false;

const avatarPlaceholders = new Map();
const avatarColors = ["#5b8def", "#e07a5f", "#81b29a", "#9b5de5", "#f2a154", "#4d908e"];

const getAvatarPlaceholder = key => {
    if (!avatarPlaceholders.has(key)) {
        const color = avatarColors[Math.floor(Math.random() * avatarColors.length)];
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80"><rect width="80" height="80" fill="${color}"/><circle cx="40" cy="29" r="15" fill="#fff" opacity=".92"/><path d="M12 80c2-18 13-28 28-28s26 10 28 28" fill="#fff" opacity=".92"/></svg>`;
        avatarPlaceholders.set(key, `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`);
    }
    return avatarPlaceholders.get(key);
};

const avatarColumnRenderer = (data, type, row, meta) => {
    if (type !== "display") return data ?? "";

    const image = document.createElement("img");
    image.className = "foto-tabla";
    image.alt = "Foto de perfil";
    image.loading = "lazy";
    image.src = typeof data === "string" && /^data:image\//i.test(data)
        ? data
        : getAvatarPlaceholder(`${meta?.settings?.sTableId || "tabla"}-${meta?.row ?? Math.random()}`);
    return image;
};

const optionButtonsRenderer = (data, type) => {
    if (type !== "display") return data ?? "";
    return `<div class="table-action-buttons">
        <button type="button" class="table-action-btn" aria-label="Editar" title="Editar">
            <span class="material-symbols-rounded" aria-hidden="true">edit</span>
        </button>
        <button type="button" class="table-action-btn table-action-delete" aria-label="Eliminar" title="Eliminar">
            <span class="material-symbols-rounded" aria-hidden="true">delete</span>
        </button>
    </div>`;
};

const readImageAsDataUrl = file => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.addEventListener("load", () => resolve(reader.result));
    reader.addEventListener("error", () => reject(reader.error || new Error("No se pudo leer la imagen.")));
    reader.readAsDataURL(file);
});

const dataTableOptions={
    // scrollX:"2000px",
    columnDefs:[
        { className:"centered", targets: [0,2,3,4,5,6] },
        { searchable: false, targets: [0,3,4,5,7] },
        { searchable: true, targets: [6] },
        { orderable: false, targets: [0,3,4,5,6,7] },
        { width: "56px", targets:[0] },
        { render: avatarColumnRenderer, targets:[0] },
        { render: optionButtonsRenderer, targets:[7] }
    ],
    pageLength: 5,
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
    petsDataTable.destroy();
    employeesDataTable.destroy();
    productsDataTable.destroy();
    providersDataTable.destroy();
    appointmentsDataTable?.destroy();
}

dataTable=$("#datatable_usuarios").DataTable(dataTableOptions);
petsDataTable=$("#datatable_mascotas").DataTable({
    ...dataTableOptions,
    columnDefs: [
        { className: "centered", targets: [1, 2, 3, 4, 5, 6, 7] },
        { searchable: false, targets: [7] },
        { orderable: false, targets: [7] },
        { width: "10%", targets: [0] },
        { render: optionButtonsRenderer, targets: [7] }
    ],
    language: {
        ...dataTableOptions.language,
        zeroRecords: "Ninguna mascota encontrada",
        infoEmpty: "Ninguna mascota encontrada"
    }
});
employeesDataTable=$("#datatable_empleados").DataTable({
    ...dataTableOptions,
    columnDefs: [
        { className: "centered", targets: [0, 1, 2, 3, 4, 5, 6, 7] },
        { searchable: false, targets: [0, 7] },
        { orderable: false, targets: [0, 7] },
        { width: "56px", targets: [0] },
        { render: avatarColumnRenderer, targets: [0] },
        { render: optionButtonsRenderer, targets: [7] }
    ],
    language: {
        ...dataTableOptions.language,
        zeroRecords: "Ningún empleado encontrado",
        infoEmpty: "Ningún empleado encontrado"
    }
});
productsDataTable=$("#datatable_productos").DataTable({
    ...dataTableOptions,
    columnDefs: [
        { className: "centered", targets: "_all" },
        { render: avatarColumnRenderer, targets: [0] },
        { render: $.fn.dataTable.render.text(), targets: [1, 2, 3, 4, 5, 6] },
        { render: optionButtonsRenderer, targets: [7] },
        { orderable: false, searchable: false, width: "56px", targets: [0, 7] }
    ],
    language: {
        ...dataTableOptions.language,
        emptyTable: "No hay productos registrados",
        zeroRecords: "Ningún producto encontrado",
        infoEmpty: "Ningún producto encontrado"
    }
});
providersDataTable=$("#datatable_proveedores").DataTable({
    ...dataTableOptions,
    columnDefs: [
        { className: "centered", targets: "_all" },
        { render: $.fn.dataTable.render.text(), targets: [0, 1, 2, 3, 4] },
        { render: optionButtonsRenderer, targets: [5] },
        { orderable: false, searchable: false, targets: [5] }
    ],
    language: {
        ...dataTableOptions.language,
        emptyTable: "No hay proveedores registrados",
        zeroRecords: "Ningún proveedor encontrado",
        infoEmpty: "Ningún proveedor encontrado"
    }
});
appointmentsDataTable=$("#datatable_citas").DataTable({
    ...dataTableOptions,
    order: [[1, "asc"]],
    columnDefs: [
        { className: "centered", targets: "_all" },
        { render: $.fn.dataTable.render.text(), targets: [0, 2, 3, 4, 5, 6] },
        {
            targets: [1],
            render: (data, type) => {
                if (type !== "display" || typeof data !== "string") return data;
                const parsedDate = new Date(`${data}T00:00`);
                if (Number.isNaN(parsedDate.getTime())) return data;
                return new Intl.DateTimeFormat("es").format(parsedDate);
            }
        }
    ],
    language: {
        ...dataTableOptions.language,
        emptyTable: "No hay citas agendadas",
        zeroRecords: "No se encontraron citas",
        infoEmpty: "No hay citas para mostrar"
    }
});
appointments.forEach(appointment => appointmentsDataTable.row.add([
    appointment.id,
    appointment.date,
    appointment.time,
    appointment.reason || "—",
    appointment.status,
    appointment.serviceId || "—",
    appointment.ci || "—"
]));
appointmentsDataTable.draw();

dataTableIsInitialized = true;
}

window.addEventListener("load", async () => {
    const panelDate = document.querySelector(".date input[type='date']");
    if (panelDate) {
        const today = new Date();
        const localToday = new Date(today.getTime() - today.getTimezoneOffset() * 60000);
        panelDate.value = localToday.toISOString().slice(0, 10);
    }

    await initDataTable();
});

    const productoForm = document.querySelector("#formProducto");
    const productoModalElement = document.querySelector("#modalProducto");

    if (productoForm && productoModalElement) {
        const imageInput = productoForm.querySelector("#productoImagen");
        if (imageInput) {
            imageInput.addEventListener("change", () => imageInput.setCustomValidity(""));
        }

        productoForm.addEventListener("submit", async event => {
            event.preventDefault();
            if (!productoForm.reportValidity()) return;

            const imageFile = imageInput?.files[0];
            if (imageFile && !imageFile.type.startsWith("image/")) {
                imageInput.setCustomValidity("Selecciona un archivo de imagen.");
                imageInput.reportValidity();
                return;
            }

            const productoValues = Array.from(new FormData(productoForm).values())
                .filter(value => typeof value === "string");
            let imageData = null;
            if (imageFile) {
                try {
                    imageData = await readImageAsDataUrl(imageFile);
                } catch (error) {
                    console.error("No se pudo leer la imagen del producto.", error);
                    window.alert("No se pudo leer la imagen del producto. Inténtalo de nuevo.");
                    return;
                }
            }

            productsDataTable.row.add([imageData, ...productoValues, null]).draw();
            productoForm.reset();
            bootstrap.Modal.getOrCreateInstance(productoModalElement).hide();
        });
    }

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
const mainSections = document.querySelectorAll("main .main-section");
const providerTablePanel = document.querySelector("#panelProveedores");
const productTablePanel = document.querySelector("#panelTablaProductos");
const tableViewButtons = document.querySelectorAll("[data-tabla-vista]");
const userClientPanel = document.querySelector("#panelUsuariosClientes");
const userEmployeePanel = document.querySelector("#panelUsuariosEmpleados");
const userViewButtons = document.querySelectorAll("[data-vista-usuario]");

const calendarDays = document.querySelector("#calendar-days");
const calendarDate = document.querySelector("#calendar-date");
const previousMonthButton = document.querySelector("#prevMonth");
const nextMonthButton = document.querySelector("#nextMonth");
const calendarToggleButton = document.querySelector("#toggleCalendario");
const calendarContainer = document.querySelector("#calendarioAgenda");

if (calendarToggleButton && calendarContainer) {
    calendarToggleButton.addEventListener("click", () => {
        const isExpanded = calendarToggleButton.getAttribute("aria-expanded") === "true";
        calendarContainer.classList.toggle("d-none", isExpanded);
        calendarToggleButton.setAttribute("aria-expanded", String(!isExpanded));
    });
}

const appointmentStorageKey = "veterinariaCitas";
let renderCalendarForAppointment = null;
const loadAppointments = () => {
    try {
        const savedAppointments = JSON.parse(localStorage.getItem(appointmentStorageKey) || "[]");
        if (!Array.isArray(savedAppointments)) {
            throw new TypeError("El calendario guardado no tiene un formato válido.");
        }
        return savedAppointments
            .filter(appointment =>
                appointment
                && typeof appointment.date === "string"
                && typeof appointment.time === "string"
                && typeof appointment.client === "string"
                && typeof appointment.pet === "string"
                && typeof appointment.service === "string"
            )
            .map((appointment, index) => ({
                ...appointment,
                id: Number.isInteger(Number(appointment.id)) && Number(appointment.id) > 0
                    ? Number(appointment.id)
                    : index + 1,
                ci: typeof appointment.ci === "string" ? appointment.ci : "",
                serviceId: typeof appointment.serviceId === "string" ? appointment.serviceId : "",
                status: ["Activo", "En espera", "Terminado"].includes(appointment.status)
                    ? appointment.status
                    : "En espera",
                reason: typeof appointment.reason === "string" ? appointment.reason : ""
            }));
    } catch (error) {
        console.error("No se pudieron cargar las citas guardadas.", error);
        return [];
    }
};
const appointments = loadAppointments();

if (calendarDays && calendarDate && previousMonthButton && nextMonthButton) {
    const today = new Date();
    const currentMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const dateKey = (year, monthIndex, day) =>
        `${year}-${String(monthIndex + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;

    const renderCalendar = month => {
        const year = month.getFullYear();
        const monthIndex = month.getMonth();
        const firstWeekday = (new Date(year, monthIndex, 1).getDay() + 6) % 7;
        const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
        const fragment = document.createDocumentFragment();

        calendarDate.textContent = new Intl.DateTimeFormat("es", {
            month: "long",
            year: "numeric"
        }).format(month);
        calendarDays.replaceChildren();

        for (let blankDay = 0; blankDay < firstWeekday; blankDay++) {
            const emptyCell = document.createElement("li");
            emptyCell.className = "calendario_dia calendario_dia-vacio";
            emptyCell.setAttribute("aria-hidden", "true");
            fragment.append(emptyCell);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = document.createElement("li");
            const isToday = year === today.getFullYear()
                && monthIndex === today.getMonth()
                && day === today.getDate();

            dayCell.className = "calendario_dia";
            if (isToday) {
                dayCell.classList.add("calendario_hoy");
                dayCell.setAttribute("aria-current", "date");
            }

            const dayHeader = document.createElement("div");
            dayHeader.className = "calendario_dia-header";

            const dayNumber = document.createElement("span");
            dayNumber.className = "calendario_numero";
            dayNumber.textContent = String(day);

            const addAppointmentButton = document.createElement("button");
            addAppointmentButton.type = "button";
            addAppointmentButton.className = "calendario_agregar-cita";
            addAppointmentButton.textContent = "+";
            addAppointmentButton.setAttribute("aria-label", `Añadir cita el ${day} de ${calendarDate.textContent}`);
            addAppointmentButton.dataset.fecha = dateKey(year, monthIndex, day);

            dayHeader.append(dayNumber, addAppointmentButton);
            dayCell.append(dayHeader);

            const dayAppointments = document.createElement("div");
            dayAppointments.className = "calendario_citas";
            dayAppointments.setAttribute("aria-label", `Citas del día ${day}`);

            appointments
                .filter(appointment => appointment.date === dateKey(year, monthIndex, day))
                .sort((first, second) => first.time.localeCompare(second.time))
                .forEach(appointment => {
                    const appointmentItem = document.createElement("div");
                    appointmentItem.className = "calendario_cita";

                    const appointmentTime = document.createElement("strong");
                    appointmentTime.textContent = appointment.time;

                    const appointmentDescription = document.createElement("span");
                    const serviceLabel = appointment.serviceId
                        ? `Servicio ${appointment.serviceId}`
                        : appointment.service;
                    appointmentDescription.textContent = `${appointment.pet} - ${serviceLabel}`;
                    appointmentDescription.title = `${appointment.client}: ${appointment.pet} - ${serviceLabel}`;

                    appointmentItem.append(appointmentTime, appointmentDescription);
                    dayAppointments.append(appointmentItem);
                });

            dayCell.append(dayAppointments);
            fragment.append(dayCell);
        }

        calendarDays.append(fragment);
    };

    renderCalendar(currentMonth);
    renderCalendarForAppointment = selectedMonth => {
        currentMonth.setFullYear(selectedMonth.getFullYear(), selectedMonth.getMonth(), 1);
        renderCalendar(currentMonth);
    };

    calendarDays.addEventListener("click", event => {
        const addButton = event.target.closest(".calendario_agregar-cita");
        if (!addButton) return;

        const citaFecha = document.querySelector("#citaFecha");
        const citaModalElement = document.querySelector("#modalCita");
        if (!citaFecha || !citaModalElement) return;

        citaFecha.value = addButton.dataset.fecha;
        bootstrap.Modal.getOrCreateInstance(citaModalElement).show();
    });

    previousMonthButton.addEventListener("click", () => {
        currentMonth.setMonth(currentMonth.getMonth() - 1);
        renderCalendar(currentMonth);
    });

    nextMonthButton.addEventListener("click", () => {
        currentMonth.setMonth(currentMonth.getMonth() + 1);
        renderCalendar(currentMonth);
    });
}

const switchUserView = view => {
    const showEmployees = view === "empleados";
    userClientPanel.classList.toggle("d-none", showEmployees);
    userEmployeePanel.classList.toggle("d-none", !showEmployees);

    userViewButtons.forEach(button => {
        const isActive = button.dataset.vistaUsuario === view;
        button.classList.toggle("active", isActive);
        button.setAttribute("aria-pressed", String(isActive));
    });

    (showEmployees ? employeesDataTable : dataTable).columns.adjust().draw(false);
};

userViewButtons.forEach(button => {
    button.addEventListener("click", () => switchUserView(button.dataset.vistaUsuario));
});

const citaForm = document.querySelector("#formCita");
const citaFormInfo = document.querySelector("#citaFormularioInfo");
const citaModalElement = document.querySelector("#modalCita");

if (citaForm && citaFormInfo && citaModalElement) {
    citaForm.addEventListener("submit", event => {
        event.preventDefault();
        if (!citaForm.reportValidity()) return;

        const formData = new FormData(citaForm);
        const ci = String(formData.get("ci")).trim();
        const serviceId = String(formData.get("serviceId")).trim();
        const appointment = {
            id: appointments.reduce((maxId, current) => Math.max(maxId, current.id), 0) + 1,
            date: String(formData.get("fecha")),
            time: String(formData.get("hora")),
            ci,
            client: ci,
            pet: String(formData.get("mascota")).trim(),
            serviceId,
            service: serviceId,
            reason: String(formData.get("motivo") || "").trim(),
            status: String(formData.get("estado"))
        };
        appointments.push(appointment);

        try {
            localStorage.setItem(appointmentStorageKey, JSON.stringify(appointments));
        } catch (error) {
            appointments.pop();
            console.error("No se pudo guardar la cita en este navegador.", error);
            window.alert("La cita no se pudo guardar. Inténtalo de nuevo.");
            return;
        }

        if (appointmentsDataTable) {
            appointmentsDataTable.row.add([
                appointment.id,
                appointment.date,
                appointment.time,
                appointment.reason || "—",
                appointment.status,
                appointment.serviceId,
                appointment.ci
            ]).draw();
        }

        if (calendarDays && calendarDate) {
            const selectedMonth = new Date(`${appointment.date}T00:00:00`);
            if (!Number.isNaN(selectedMonth.getTime())) {
                renderCalendarForAppointment(selectedMonth);
            }
        }

        citaFormInfo.textContent = "La cita se agregó al calendario.";
        citaFormInfo.classList.remove("d-none");
        citaForm.reset();
        bootstrap.Modal.getOrCreateInstance(citaModalElement).hide();
    });

    citaModalElement.addEventListener("show.bs.modal", () => {
        citaFormInfo.classList.add("d-none");
    });
}

tableViewButtons.forEach(button => {
    button.addEventListener("click", () => {
        const showProviders = button.dataset.tablaVista === "proveedores";
        providerTablePanel.classList.toggle("d-none", !showProviders);
        productTablePanel.classList.toggle("d-none", showProviders);

        tableViewButtons.forEach(viewButton => {
            const isActive = viewButton === button;
            viewButton.classList.toggle("active", isActive);
            viewButton.setAttribute("aria-pressed", String(isActive));
        });

        (showProviders ? providersDataTable : productsDataTable).columns.adjust().draw(false);
    });
});

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

        if (["clientes", "empleados"].includes(targetId)) {
            switchUserView(targetId);
        }

        const sectionId = targetId === "empleados" ? "clientes" : targetId;
        mainSections.forEach(section => {
            section.classList.toggle("active", section.id === sectionId);
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

const departamentoSearchConfigs = [
    {
        select: document.querySelector("#clienteDepartamento"),
        toggle: document.querySelector("#clienteDepartamentoToggle"),
        menu: document.querySelector("#clienteDepartamentoMenu"),
        search: document.querySelector("#buscarDepartamento"),
        noResults: document.querySelector("#departamentoSinResultados")
    },
    {
        select: document.querySelector("#empleadoDepartamento"),
        toggle: document.querySelector("#empleadoDepartamentoToggle"),
        menu: document.querySelector("#empleadoDepartamentoMenu"),
        search: document.querySelector("#buscarEmpleadoDepartamento"),
        noResults: document.querySelector("#empleadoDepartamentoSinResultados")
    }
];
const clienteMascotaCheckbox = document.querySelector("#checkDefault");
const petFormCheckboxes = [
    document.querySelector("#checkDefault"),
    document.querySelector("#empleadoMascotaCheck")
].filter(Boolean);
const clienteMascotaModalElement = document.querySelector("#modalMascotaCliente");

if (petFormCheckboxes.length && clienteMascotaModalElement) {
    const clienteMascotaModal = bootstrap.Modal.getOrCreateInstance(clienteMascotaModalElement);
    let mascotasClienteConfirmadas = false;

    petFormCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", () => {
            if (checkbox.checked) {
                mascotasClienteConfirmadas = false;
                clienteMascotaModal.show();
            } else {
                clienteMascotaModal.hide();
            }
        });
    });

    clienteMascotaModalElement.addEventListener("hidden.bs.modal", () => {
        if (!mascotasClienteConfirmadas) {
            petFormCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
        mascotasClienteConfirmadas = false;
    });

    const formMascotaCliente = document.querySelector("#formMascotaCliente");
    if (formMascotaCliente) {
        formMascotaCliente.addEventListener("submit", event => {
            event.preventDefault();
            if (!formMascotaCliente.reportValidity()) return;

            mascotasClienteConfirmadas = true;
            clienteMascotaModal.hide();
        });
    }
}

const mascotasClienteContainer = document.querySelector("#mascotasClienteContainer");
const agregarOtraMascotaButton = document.querySelector("#agregarOtraMascota");
let mascotaEntryCounter = 1;

if (mascotasClienteContainer && agregarOtraMascotaButton) {
    agregarOtraMascotaButton.addEventListener("click", () => {
        mascotaEntryCounter++;
        const entryNumber = mascotaEntryCounter;
        const firstEntry = mascotasClienteContainer.querySelector(".mascota-cliente-entry");
        const newEntry = firstEntry.cloneNode(true);

        newEntry.querySelector(".mascota-cliente-title").textContent = `Mascota ${entryNumber}`;
        newEntry.querySelectorAll("input, select").forEach(field => {
            const previousId = field.id;
            field.id = `${previousId}-${entryNumber}`;
            field.name = `mascotas[${entryNumber - 1}][${field.name}]`;

            const label = newEntry.querySelector(`label[for="${previousId}"]`);
            if (label) label.htmlFor = field.id;

            if (field.tagName === "SELECT") {
                field.selectedIndex = 0;
            } else {
                field.value = "";
            }
        });

        const removeButton = document.createElement("button");
        removeButton.type = "button";
        removeButton.className = "btn btn-link text-danger p-0 mb-3";
        removeButton.textContent = "Quitar esta mascota";
        removeButton.addEventListener("click", () => newEntry.remove());
        newEntry.append(removeButton);

        mascotasClienteContainer.append(newEntry);
        newEntry.querySelector("input").focus();
    });
}

const normalizeDepartmentText = text => text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLocaleLowerCase("es");

departamentoSearchConfigs.forEach(({ select, toggle, menu, search, noResults }) => {
    if (!select || !toggle || !menu || !search) return;

    Array.from(select.options).forEach(option => {
        if (!option.value) return;

        const item = document.createElement("button");
        item.type = "button";
        item.className = "dropdown-item";
        item.textContent = option.textContent;
        item.addEventListener("click", () => {
            select.value = option.value;
            select.dispatchEvent(new Event("change", { bubbles: true }));
            bootstrap.Dropdown.getOrCreateInstance(toggle).hide();
        });

        const listItem = document.createElement("li");
        listItem.append(item);
        menu.append(listItem);
    });

    const departmentItems = Array.from(menu.querySelectorAll(".dropdown-item"));
    const filterDepartamentos = () => {
        const query = normalizeDepartmentText(search.value.trim());
        let visibleCount = 0;

        departmentItems.forEach(item => {
            const matches = normalizeDepartmentText(item.textContent).includes(query);
            item.closest("li").hidden = !matches;
            if (matches) visibleCount++;
        });

        if (noResults) {
            noResults.classList.toggle("d-none", visibleCount > 0);
        }
    };

    search.addEventListener("input", filterDepartamentos);
    toggle.addEventListener("shown.bs.dropdown", () => search.focus());
    toggle.addEventListener("hidden.bs.dropdown", () => {
        search.value = "";
        filterDepartamentos();
    });

    select.addEventListener("change", () => {
        const hasSelection = Boolean(select.value);
        toggle.textContent = select.selectedOptions[0].textContent;
        toggle.classList.toggle("is-placeholder", !hasSelection);
    });

    toggle.classList.toggle("is-placeholder", !select.value);
});