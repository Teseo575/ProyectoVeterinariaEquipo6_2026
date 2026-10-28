// Datos de la tabla
let dataTable
let petsDataTable
let employeesDataTable
let productsDataTable
let providersDataTable
let appointmentsDataTable
let reportsDataTable
let dataTableIsInitialized=false;

const reportStorageKey = "veterinariaReportes";
const loadReports = () => {
    try {
        const savedReports = JSON.parse(localStorage.getItem(reportStorageKey) || "[]");
        if (!Array.isArray(savedReports)) {
            throw new TypeError("El historial guardado no tiene un formato válido.");
        }
        return savedReports.filter(report =>
            report
            && Number.isInteger(Number(report.id))
            && typeof report.user === "string"
            && typeof report.action === "string"
            && typeof report.date === "string"
            && typeof report.time === "string"
        );
    } catch (error) {
        console.error("No se pudieron cargar los reportes guardados.", error);
        window.alert("No se pudo cargar el historial de reportes.");
        return [];
    }
};
const reports = loadReports();

const recordReportAction = action => {
    const now = new Date();
    const report = {
        id: reports.reduce((maxId, current) => Math.max(maxId, Number(current.id) || 0), 0) + 1,
        user: document.querySelector(".profile .info p b")?.textContent.trim() || "Usuario",
        action,
        date: now.toLocaleDateString("es-UY"),
        time: now.toLocaleTimeString("es-UY", { hour12: false })
    };
    reports.push(report);

    try {
        localStorage.setItem(reportStorageKey, JSON.stringify(reports));
    } catch (error) {
        console.error("No se pudo guardar el historial de reportes.", error);
        window.alert("La acción se realizó, pero no se pudo guardar en el historial.");
    }

    reportsDataTable?.row.add([
        report.id,
        report.user,
        report.action,
        report.date,
        report.time
    ]).draw(false);
};

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

const userAvatarColumnRenderer = (data, type, row, meta) => {
    if (type !== "display") return data ?? "";

    const button = document.createElement("button");
    button.type = "button";
    button.className = "perfil-foto-trigger";
    button.setAttribute("aria-label", `Ver perfil de ${row?.[2] || "usuario"}`);
    button.append(avatarColumnRenderer(data, type, row, meta));
    return button;
};

const optionButtonsRenderer = (data, type) => {
    if (type !== "display") return data ?? "";
    return `<div class="table-action-buttons">
        <button type="button" class="table-action-btn" data-table-action="edit" aria-label="Modificar" title="Modificar">
            <span class="material-symbols-rounded" aria-hidden="true">edit</span>
        </button>
        <button type="button" class="table-action-btn table-action-delete" data-table-action="delete" aria-label="Eliminar" title="Eliminar">
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
        { className:"centered", targets: [0,2,3,4,5,6,7] },
        { searchable: false, targets: [0,3,4,5,7] },
        { searchable: true, targets: [6] },
        { orderable: false, targets: [0,3,4,5,6,7] },
        { width: "56px", targets:[0] },
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
    reportsDataTable?.destroy();
}

dataTable=$("#datatable_usuarios").DataTable({
    ...dataTableOptions,
    columnDefs: [
        ...dataTableOptions.columnDefs,
        { searchable: false, targets: [0, 3, 5, 6, 8] },
        { searchable: true, targets: [4, 7] },
        { orderable: false, targets: [0, 3, 5, 6, 7, 8] },
        { render: $.fn.dataTable.render.text(), targets: [7] },
        { render: userAvatarColumnRenderer, targets: [0] },
        { render: optionButtonsRenderer, targets: [8] }
    ]
});
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
        { render: userAvatarColumnRenderer, targets: [0] },
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
        { render: $.fn.dataTable.render.text(), targets: [0, 1, 2, 3, 4, 5] },
        { render: optionButtonsRenderer, targets: [6] },
        { orderable: false, searchable: false, targets: [6] }
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
        { render: optionButtonsRenderer, targets: [7] },
        { orderable: false, searchable: false, targets: [7] },
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
    appointment.ci || "—",
    null
]));
appointmentsDataTable.draw();

reportsDataTable = $("#datatable_reportes").DataTable({
    data: reports.map(report => [
        report.id,
        report.user,
        report.action,
        report.date,
        report.time
    ]),
    order: [[0, "desc"]],
    pageLength: 10,
    columnDefs: [
        { className: "centered", targets: [0, 3, 4] },
        { render: $.fn.dataTable.render.text(), targets: "_all" },
        { orderable: false, targets: [2] }
    ],
    language: {
        ...dataTableOptions.language,
        emptyTable: "No hay acciones registradas",
        zeroRecords: "No se encontraron reportes",
        infoEmpty: "No hay reportes para mostrar"
    }
});

dataTableIsInitialized = true;
}

const stockThresholdInput = document.querySelector("#stockThreshold");
const renderStockAnalytics = () => {
    const stockRows = document.querySelector("#stockAnalyticsRows");
    const stockTableContainer = document.querySelector("#stockAnalyticsTableContainer");
    const alertMessage = document.querySelector("#stockAlert");
    if (!stockRows || !stockTableContainer || !alertMessage || !productsDataTable) return;

    const requestedThreshold = stockThresholdInput?.valueAsNumber;
    const threshold = Number.isInteger(requestedThreshold) && requestedThreshold >= 0
        ? requestedThreshold
        : 5;
    const products = productsDataTable.rows().data().toArray();
    const providerNames = new Map(
        providersDataTable.rows().data().toArray().map(provider => [String(provider[0]), String(provider[1])])
    );
    const inventory = products.map(product => ({
        name: String(product[1] || "Producto sin nombre"),
        category: String(product[4] || "—"),
        provider: providerNames.get(String(product[6])) || "—",
        stock: Number(product[5])
    }));
    const lowStock = inventory.filter(product => !Number.isFinite(product.stock) || product.stock <= threshold);
    const availableUnits = inventory.reduce((sum, product) =>
        sum + (Number.isFinite(product.stock) ? Math.max(product.stock, 0) : 0), 0);

    document.querySelector("#stockProductsCount").textContent = String(inventory.length);
    document.querySelector("#stockUnitsCount").textContent = String(availableUnits);
    document.querySelector("#stockLowCount").textContent = String(lowStock.length);
    alertMessage.classList.toggle("is-warning", lowStock.length > 0);
    alertMessage.classList.toggle("is-success", inventory.length > 0 && lowStock.length === 0);
    if (lowStock.length) {
        const productNames = lowStock.slice(0, 3).map(product => product.name).join(", ");
        const remainingCount = lowStock.length - 3;
        const remaining = remainingCount > 0 ? ` y ${remainingCount} más` : "";
        alertMessage.textContent = `${lowStock.length} ${lowStock.length === 1 ? "producto necesita" : "productos necesitan"} reposición: ${productNames}${remaining}.`;
    } else {
        alertMessage.textContent = inventory.length
            ? `No hay productos con stock bajo (umbral: ${threshold} unidades).`
            : "No hay productos con stock bajo.";
    }

    const rows = document.createDocumentFragment();
    inventory.forEach(product => {
        const row = document.createElement("tr");
        const isLow = !Number.isFinite(product.stock) || product.stock <= threshold;
        const values = [
            product.name,
            product.category,
            product.provider,
            Number.isFinite(product.stock) ? String(product.stock) : "Sin dato"
        ];
        values.forEach(value => {
            const cell = document.createElement("td");
            cell.textContent = value;
            row.append(cell);
        });
        const statusCell = document.createElement("td");
        const status = document.createElement("span");
        status.className = `stock-status${isLow ? " is-low" : ""}`;
        status.textContent = !Number.isFinite(product.stock)
            ? "Verificar stock"
            : product.stock === 0
                ? "Agotado"
                : isLow
                    ? "Stock bajo"
                    : "Disponible";
        statusCell.append(status);
        row.append(statusCell);
        rows.append(row);
    });
    stockRows.replaceChildren(rows);
    stockTableContainer.classList.toggle("d-none", inventory.length === 0);
};

stockThresholdInput?.addEventListener("input", renderStockAnalytics);

window.addEventListener("load", async () => {
    const panelDate = document.querySelector(".date input[type='date']");
    if (panelDate) {
        const today = new Date();
        const localToday = new Date(today.getTime() - today.getTimezoneOffset() * 60000);
        panelDate.value = localToday.toISOString().slice(0, 10);
    }

    await initDataTable();
    productsDataTable.on("draw", renderStockAnalytics);
    renderStockAnalytics();
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
            recordReportAction(`Agregó un producto: ${productoValues[0]}`);
            productoForm.reset();
            bootstrap.Modal.getOrCreateInstance(productoModalElement).hide();
        });
    }

const clientForm = document.querySelector("#formCliente");
const petForm = document.querySelector("#formMascota");

const nextTableId = table => table
    .column(0)
    .data()
    .toArray()
    .reduce((maxId, currentId) => Math.max(maxId, Number(currentId) || 0), 0) + 1;

const nestedPetEntries = () => Array.from(
    document.querySelectorAll("#mascotasClienteContainer .mascota-cliente-entry")
).map(entry => ({
    name: entry.querySelector('input[id^="clienteMascotaNombre"]')?.value.trim() || "",
    species: entry.querySelector('select[id^="clienteMascotaEspecie"]')?.value || "",
    breed: entry.querySelector('input[id^="clienteMascotaRaza"]')?.value.trim() || "",
    ci: entry.querySelector('input[id^="clienteMascotaTutor"]')?.value.trim() || ""
}));

if (clientForm) {
    clientForm.addEventListener("submit", event => {
        event.preventDefault();
        if (!clientForm.reportValidity()) return;

        const clientHasPets = clienteMascotaCheckbox?.checked === true;
        if (clientHasPets && !formMascotaCliente?.reportValidity()) {
            clienteMascotaModal?.show();
            return;
        }

        const formData = new FormData(clientForm);
        const clientName = `${formData.get("nombre")} ${formData.get("apellido")}`.trim();
        const clientCi = String(formData.get("ci")).trim();
        const pets = clientHasPets ? nestedPetEntries() : [];
        const petNames = pets.filter(pet => pet.name).map(pet => pet.name).join(", ");

        dataTable.row.add([
            null,
            clientCi,
            clientName,
            String(formData.get("email")).trim(),
            String(formData.get("telefono")).trim(),
            String(formData.get("direccion")).trim(),
            String(formData.get("departamento")).trim(),
            petNames || "—",
            null
        ]).draw();
        recordReportAction(`Agregó un cliente: ${clientName}`);

        pets.forEach(pet => {
            if (!pet.name) return;
            petsDataTable.row.add([
                nextTableId(petsDataTable),
                pet.name,
                pet.species,
                pet.breed,
                "—",
                clientName,
                pet.ci || clientCi,
                null
            ]).draw();
            recordReportAction(`Agregó una mascota: ${pet.name}`);
        });

        clientForm.reset();
        formMascotaCliente?.reset();
        mascotasClienteConfirmadas = false;
        bootstrap.Modal.getOrCreateInstance(document.querySelector("#modalAgregarCliente")).hide();
    });
}

if (petForm) {
    petForm.addEventListener("submit", event => {
        event.preventDefault();
        if (!petForm.reportValidity()) return;

        const formData = new FormData(petForm);
        const petName = String(formData.get("nombre")).trim();
        petsDataTable.row.add([
            nextTableId(petsDataTable),
            petName,
            String(formData.get("especie")).trim(),
            String(formData.get("raza")).trim(),
            "—",
            "—",
            String(formData.get("tutor")).trim(),
            null
        ]).draw();
        recordReportAction(`Agregó una mascota: ${petName}`);
        petForm.reset();
        bootstrap.Modal.getOrCreateInstance(document.querySelector("#modalAgregarMascota")).hide();
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
const analyticsYearSelect = document.querySelector("#analyticsYear");
const analyticsSalesChart = document.querySelector("#analyticsSalesChart");
const analyticsSalesStorageKey = "veterinariaVentas";
const analyticsReviewsStorageKey = "veterinariaResenas";
const analyticsMonthNames = Array.from({ length: 12 }, (_, month) =>
    new Intl.DateTimeFormat("es-UY", { month: "short" }).format(new Date(2026, month, 1))
);

const readAnalyticsRecords = (storageKey, recordName, isValidRecord) => {
    try {
        const records = JSON.parse(localStorage.getItem(storageKey) || "[]");
        if (!Array.isArray(records)) {
            throw new TypeError(`El registro de ${recordName} no tiene un formato válido.`);
        }
        return records.filter(isValidRecord);
    } catch (error) {
        console.error(`No se pudieron cargar los registros de ${recordName}.`, error);
        window.alert(`No se pudieron cargar los registros de ${recordName}.`);
        return [];
    }
};

const analyticsSales = readAnalyticsRecords(
    analyticsSalesStorageKey,
    "ventas",
    sale => sale
        && typeof sale.date === "string"
        && /^\d{4}-\d{2}-\d{2}/.test(sale.date)
        && ["online", "local"].includes(String(sale.channel).toLowerCase())
        && Number.isFinite(Number(sale.amount))
        && Number(sale.amount) >= 0
);
const analyticsReviews = readAnalyticsRecords(
    analyticsReviewsStorageKey,
    "reseñas",
    review => review && Number.isFinite(Number(review.rating))
        && Number(review.rating) >= 1 && Number(review.rating) <= 5
);
const analyticsCurrency = new Intl.NumberFormat("es-UY", {
    style: "currency",
    currency: "UYU",
    maximumFractionDigits: 0
});

const renderAnalytics = () => {
    if (!analyticsYearSelect || !analyticsSalesChart) return;

    const selectedYear = Number(analyticsYearSelect.value);
    const salesByMonth = Array.from({ length: 12 }, () => ({ online: 0, local: 0 }));
    analyticsSales.forEach(sale => {
        const saleDate = new Date(`${sale.date.slice(0, 10)}T00:00:00`);
        if (saleDate.getFullYear() !== selectedYear) return;
        const channel = String(sale.channel).toLowerCase();
        salesByMonth[saleDate.getMonth()][channel] += Number(sale.amount);
    });

    const maxMonthlySales = Math.max(0, ...salesByMonth.map(month => month.online + month.local));
    const chartFragment = document.createDocumentFragment();
    const rowsFragment = document.createDocumentFragment();
    let onlineTotal = 0;
    let localTotal = 0;

    salesByMonth.forEach((monthSales, monthIndex) => {
        const monthTotal = monthSales.online + monthSales.local;
        onlineTotal += monthSales.online;
        localTotal += monthSales.local;

        const monthColumn = document.createElement("div");
        monthColumn.className = "analytics-chart-month";
        const bars = document.createElement("div");
        bars.className = "analytics-chart-bars";
        bars.setAttribute("aria-hidden", "true");

        [["online", monthSales.online], ["local", monthSales.local]].forEach(([channel, amount]) => {
            const bar = document.createElement("span");
            bar.className = `analytics-chart-bar ${channel}`;
            bar.style.height = maxMonthlySales > 0 ? `${amount / maxMonthlySales * 100}%` : "0";
            bar.title = `${analyticsMonthNames[monthIndex]}: ${channel === "online" ? "Online" : "Local"} ${analyticsCurrency.format(amount)}`;
            bars.append(bar);
        });

        const monthLabel = document.createElement("small");
        monthLabel.textContent = analyticsMonthNames[monthIndex];
        monthColumn.append(bars, monthLabel);
        chartFragment.append(monthColumn);

        const row = document.createElement("tr");
        [
            analyticsMonthNames[monthIndex],
            analyticsCurrency.format(monthSales.online),
            analyticsCurrency.format(monthSales.local),
            analyticsCurrency.format(monthTotal)
        ].forEach(value => {
            const cell = document.createElement("td");
            cell.textContent = value;
            row.append(cell);
        });
        rowsFragment.append(row);
    });

    analyticsSalesChart.replaceChildren(chartFragment);
    document.querySelector("#analyticsMonthlyRows")?.replaceChildren(rowsFragment);

    const totalRow = document.createElement("tr");
    ["Total anual", analyticsCurrency.format(onlineTotal), analyticsCurrency.format(localTotal),
        analyticsCurrency.format(onlineTotal + localTotal)].forEach(value => {
        const cell = document.createElement("td");
        cell.textContent = value;
        totalRow.append(cell);
    });
    document.querySelector("#analyticsMonthlyTotals")?.replaceChildren(totalRow);
    const yearSummary = document.querySelector("#analyticsYearSummary");
    if (yearSummary) yearSummary.textContent = `Totales por canal durante ${selectedYear}.`;

    const completedAppointments = appointments.filter(appointment =>
        String(appointment.status).toLocaleLowerCase("es") === "terminado"
        && String(appointment.date).startsWith(String(selectedYear))
    );
    const attendedPets = new Set(completedAppointments.map(appointment =>
        `${appointment.ci || appointment.client || ""}:${appointment.pet || ""}`.toLocaleLowerCase("es")
    ));
    const petMetric = document.querySelector("#analyticsPetsAttended");
    if (petMetric) petMetric.textContent = String(attendedPets.size);

    const registeredUsersMetric = document.querySelector("#analyticsUsersRegistered");
    if (registeredUsersMetric) {
        registeredUsersMetric.textContent = String(dataTable?.rows().count() || 0);
    }

    const reviewMetric = document.querySelector("#analyticsReviews");
    if (reviewMetric) reviewMetric.textContent = String(analyticsReviews.length);
    const ratingMetric = document.querySelector("#analyticsReviewRating");
    if (ratingMetric) {
        const averageRating = analyticsReviews.length
            ? analyticsReviews.reduce((sum, review) => sum + Number(review.rating), 0) / analyticsReviews.length
            : null;
        ratingMetric.textContent = averageRating === null
            ? "Sin calificación"
            : `Promedio ${averageRating.toLocaleString("es-UY", { maximumFractionDigits: 1 })} / 5`;
    }
};

if (analyticsYearSelect) {
    const currentYear = new Date().getFullYear();
    const knownYears = new Set(Array.from({ length: 5 }, (_, offset) => currentYear - offset));
    analyticsSales.forEach(record => {
        const year = Number(String(record.date).slice(0, 4));
        if (Number.isInteger(year) && year > 0) knownYears.add(year);
    });
    [...knownYears].sort((first, second) => second - first).forEach(year => {
        const option = document.createElement("option");
        option.value = String(year);
        option.textContent = String(year);
        analyticsYearSelect.append(option);
    });
    analyticsYearSelect.value = String(currentYear);
    analyticsYearSelect.addEventListener("change", renderAnalytics);
}
const serviceCards = document.querySelectorAll("#resumenServicios [data-service-panel]");
const serviceOverview = document.querySelector("#resumenServicios");
const serviceDetail = document.querySelector("#detalleServicio");
const serviceBackButton = document.querySelector("#volverServicios");
const serviceDetailTitle = document.querySelector("#tituloDetalleServicio");
const serviceDetailQuantity = document.querySelector("#cantidadDetalleServicio");
const serviceDetailIndicator = document.querySelector("#indicadorDetalleServicio");
const serviceDetailStatus = document.querySelector("#estadoDetalleServicio");
let selectedServiceCard = null;
const providerTablePanel = document.querySelector("#panelProveedores");
const productTablePanel = document.querySelector("#panelTablaProductos");
const tableViewButtons = document.querySelectorAll("[data-tabla-vista]");
const addProductButton = document.querySelector("#btnAgregarProducto");
const addProviderButton = document.querySelector("#btnAgregarProveedor");
const userClientPanel = document.querySelector("#panelUsuariosClientes");
const userEmployeePanel = document.querySelector("#panelUsuariosEmpleados");
const userViewButtons = document.querySelectorAll("[data-vista-usuario]");
const userProfileSection = document.querySelector("#perfilUsuario");
const userProfileBackButton = document.querySelector("#volverUsuariosPerfil");
const userProfilePhoto = document.querySelector("#perfilUsuarioFoto");
const userProfileName = document.querySelector("#perfilUsuarioTitulo");
const userProfileType = document.querySelector("#perfilUsuarioTipo");
const userProfileData = document.querySelector("#perfilUsuarioDatos");

[
    {
        tableId: "datatable_usuarios",
        type: "Cliente",
        fields: [
            ["Cédula", 1],
            ["Nombre", 2],
            ["Correo electrónico", 3],
            ["Teléfono", 4],
            ["Dirección", 5],
            ["Departamento", 6],
            ["Mascotas", 7]
        ]
    },
    {
        tableId: "datatable_empleados",
        type: "Empleado",
        fields: [
            ["Cédula", 1],
            ["Nombre", 2],
            ["Cargo", 3],
            ["Correo electrónico", 4],
            ["Teléfono", 5],
            ["Dirección", 6]
        ]
    }
].forEach(({ tableId, type, fields }) => {
    const tableElement = document.getElementById(tableId);
    if (!tableElement) return;

    tableElement.addEventListener("click", event => {
        const trigger = event.target.closest(".perfil-foto-trigger");
        if (!trigger || !userProfileSection) return;

        const row = $(tableElement).DataTable().row(trigger.closest("tr")).data();
        if (!row) {
            console.error(`No se pudieron cargar los datos del perfil de ${type.toLowerCase()}.`);
            return;
        }

        const name = String(row[2] || type);
        userProfileName.textContent = name;
        userProfileType.textContent = type;
        userProfilePhoto.src = trigger.querySelector("img")?.src || getAvatarPlaceholder(tableId);
        userProfileData.replaceChildren();

        fields.forEach(([label, columnIndex]) => {
            const card = document.createElement("div");
            card.className = "perfil-usuario-dato";
            const fieldLabel = document.createElement("span");
            fieldLabel.className = "perfil-usuario-etiqueta";
            fieldLabel.textContent = label;
            const fieldValue = document.createElement("strong");
            fieldValue.textContent = String(row[columnIndex] || "—");
            card.append(fieldLabel, fieldValue);
            userProfileData.append(card);
        });

        mainSections.forEach(section => section.classList.toggle("active", section === userProfileSection));
        sidebarLinks.forEach(link => link.classList.toggle("active", link.dataset.section === "clientes"));
    });
});

userProfileBackButton?.addEventListener("click", () => {
    mainSections.forEach(section => section.classList.toggle("active", section.id === "clientes"));
    sidebarLinks.forEach(link => link.classList.toggle("active", link.dataset.section === "clientes"));
});

const addFormPanels = [
    {
        formPanelId: "formAgregarClientePanel",
        tableId: "datatable_usuarios",
        label: "Agregar cliente",
        modalId: "modalAgregarCliente"
    },
    {
        formPanelId: "formAgregarEmpleadoPanel",
        tableId: "datatable_empleados",
        label: "Agregar empleado",
        modalId: "modalAgregarEmpleado"
    },
    {
        formPanelId: "formAgregarMascotaPanel",
        tableId: "datatable_mascotas",
        label: "Agregar mascota",
        modalId: "modalAgregarMascota"
    }
];

addFormPanels.forEach(({ formPanelId, tableId, label, modalId }) => {
    const formPanel = document.getElementById(formPanelId);
    const table = document.getElementById(tableId);
    const tableWrapper = table?.closest(".table-responsive");
    if (!formPanel || !tableWrapper || !tableWrapper.parentElement) return;

    const modal = document.createElement("div");
    modal.className = "modal fade";
    modal.id = modalId;
    modal.tabIndex = -1;
    modal.setAttribute("aria-hidden", "true");

    const modalDialog = document.createElement("div");
    modalDialog.className = "modal-dialog modal-dialog-centered modal-dialog-scrollable";
    const modalContent = document.createElement("div");
    modalContent.className = "modal-content";
    const modalHeader = document.createElement("div");
    modalHeader.className = "modal-header";
    const modalBody = document.createElement("div");
    modalBody.className = "modal-body";
    const heading = formPanel.querySelector("h2");

    if (heading) {
        heading.className = "modal-title fs-5";
        heading.id = `${modalId}Label`;
        modal.setAttribute("aria-labelledby", heading.id);
        modalHeader.append(heading);
    } else {
        modal.setAttribute("aria-label", label);
    }

    const closeButton = document.createElement("button");
    closeButton.type = "button";
    closeButton.className = "btn-close";
    closeButton.dataset.bsDismiss = "modal";
    closeButton.setAttribute("aria-label", "Cerrar");
    modalHeader.append(closeButton);

    const toggleButton = document.createElement("button");
    toggleButton.type = "button";
    toggleButton.className = "btn btn-primary mb-3";
    toggleButton.textContent = label;
    toggleButton.dataset.bsToggle = "modal";
    toggleButton.dataset.bsTarget = `#${modalId}`;

    tableWrapper.parentElement.insertBefore(toggleButton, tableWrapper);
    formPanel.classList.remove("mt-4");
    modalBody.append(formPanel);
    modalContent.append(modalHeader, modalBody);
    modalDialog.append(modalContent);
    modal.append(modalDialog);
    document.body.append(modal);
});

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
                appointment.ci,
                null
            ]).draw();
        }
        recordReportAction(`Agendó una cita para ${appointment.pet} el ${appointment.date} a las ${appointment.time}`);

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
        addProductButton.classList.toggle("d-none", showProviders);
        addProviderButton.classList.toggle("d-none", !showProviders);

        tableViewButtons.forEach(viewButton => {
            const isActive = viewButton === button;
            viewButton.classList.toggle("active", isActive);
            viewButton.setAttribute("aria-pressed", String(isActive));
        });

        (showProviders ? providersDataTable : productsDataTable).columns.adjust().draw(false);
    });
});

const providerForm = document.querySelector("#formProveedor");
const providerModalElement = document.querySelector("#modalProveedor");

const supplierOrdersStorageKey = "veterinariaPedidosProveedor";
const supplierOrdersStatus = ["Pendiente", "Enviado", "Recibido", "Cancelado"];
const supplierOrdersCurrency = new Intl.NumberFormat("es-UY", {
    style: "currency",
    currency: "UYU",
    maximumFractionDigits: 2
});
const loadSupplierOrders = () => {
    try {
        const savedOrders = JSON.parse(localStorage.getItem(supplierOrdersStorageKey) || "[]");
        if (!Array.isArray(savedOrders)) {
            throw new TypeError("El historial de pedidos tiene un formato no válido.");
        }
        return savedOrders.filter(order =>
            order
            && typeof order.id === "string"
            && typeof order.date === "string"
            && typeof order.provider === "string"
            && Array.isArray(order.items)
            && supplierOrdersStatus.includes(order.status)
        );
    } catch (error) {
        console.error("No se pudieron cargar los pedidos a proveedores.", error);
        window.alert("No se pudo cargar el historial de pedidos a proveedores.");
        return [];
    }
};
const supplierOrders = loadSupplierOrders();
const supplierOrdersRows = document.querySelector("#pedidosProveedorRows");
const supplierOrdersEmpty = document.querySelector("#pedidosProveedorVacio");
const supplierOrderForm = document.querySelector("#formPedidoProveedor");
const supplierOrderModal = document.querySelector("#modalPedidoProveedor");
const supplierSelect = document.querySelector("#pedidoProveedor");
const supplierOrderItems = document.querySelector("#pedidoArticulos");
const supplierOrderTotal = document.querySelector("#pedidoTotal");
const supplierOrderDeliveryDate = document.querySelector("#pedidoFechaEntrega");
const supplierOrderNotes = document.querySelector("#pedidoNotas");

const persistSupplierOrders = nextOrders => {
    try {
        localStorage.setItem(supplierOrdersStorageKey, JSON.stringify(nextOrders));
    } catch (error) {
        console.error("No se pudieron guardar los pedidos a proveedores.", error);
        window.alert("No se pudo guardar el pedido. Comprueba el espacio disponible e inténtalo de nuevo.");
        return false;
    }

    supplierOrders.splice(0, supplierOrders.length, ...nextOrders);
    renderSupplierOrders();
    return true;
};

const renderSupplierOrders = () => {
    if (!supplierOrdersRows || !supplierOrdersEmpty) return;
    supplierOrdersRows.replaceChildren();

    supplierOrders.forEach(order => {
        const row = document.createElement("tr");
        const itemSummary = order.items.map(item => `${item.quantity} × ${item.name}`).join(", ");
        const values = [
            order.id,
            new Intl.DateTimeFormat("es-UY").format(new Date(`${order.date}T00:00:00`)),
            order.provider,
            itemSummary,
            supplierOrdersCurrency.format(order.total)
        ];
        values.forEach(value => {
            const cell = document.createElement("td");
            cell.textContent = value;
            row.append(cell);
        });

        const statusCell = document.createElement("td");
        const statusSelect = document.createElement("select");
        statusSelect.className = "form-select form-select-sm";
        statusSelect.setAttribute("aria-label", `Estado del pedido ${order.id}`);
        statusSelect.dataset.pedidoEstado = order.id;
        supplierOrdersStatus.forEach(status => {
            const option = new Option(status, status, false, status === order.status);
            statusSelect.append(option);
        });
        statusCell.append(statusSelect);
        row.append(statusCell);

        const actionsCell = document.createElement("td");
        const deleteButton = document.createElement("button");
        deleteButton.type = "button";
        deleteButton.className = "btn btn-sm btn-outline-danger";
        deleteButton.dataset.pedidoEliminar = order.id;
        deleteButton.textContent = "Eliminar";
        deleteButton.setAttribute("aria-label", `Eliminar pedido ${order.id}`);
        actionsCell.append(deleteButton);
        row.append(actionsCell);
        supplierOrdersRows.append(row);
    });

    supplierOrdersEmpty.classList.toggle("d-none", supplierOrders.length > 0);
};

const readSupplierRows = () => providersDataTable.rows().data().toArray();
const readProductRows = () => productsDataTable.rows().data().toArray();

const refreshSupplierSelect = () => {
    if (!supplierSelect) return;
    supplierSelect.replaceChildren(new Option("Selecciona un proveedor", ""));
    readSupplierRows().forEach(provider => {
        const option = new Option(`${provider[1]} — ${provider[2]}`, String(provider[0]));
        option.dataset.providerName = String(provider[1]);
        supplierSelect.append(option);
    });
};

const createSupplierOrderItemRow = () => {
    if (!supplierOrderItems) return;

    const row = document.createElement("tr");
    const productCell = document.createElement("td");
    const productSelect = document.createElement("select");
    productSelect.className = "form-select";
    productSelect.setAttribute("aria-label", "Producto");
    productSelect.required = true;
    productSelect.append(new Option("Selecciona un producto", ""));

    const providerId = supplierSelect?.value;
    if (providerId) {
        readProductRows()
            .filter(product => String(product[6]) === providerId)
            .forEach(product => {
                const option = new Option(String(product[1]), String(product[1]));
                option.dataset.price = String(Number(product[2]) || 0);
                productSelect.append(option);
            });
    }
    productCell.append(productSelect);

    const quantityCell = document.createElement("td");
    const quantityInput = document.createElement("input");
    quantityInput.className = "form-control";
    quantityInput.type = "number";
    quantityInput.min = "1";
    quantityInput.step = "1";
    quantityInput.value = "1";
    quantityInput.required = true;
    quantityInput.setAttribute("aria-label", "Cantidad");
    quantityCell.append(quantityInput);

    const priceCell = document.createElement("td");
    const subtotalCell = document.createElement("td");
    const removeCell = document.createElement("td");
    const removeButton = document.createElement("button");
    removeButton.type = "button";
    removeButton.className = "btn pedido-articulo-eliminar";
    removeButton.textContent = "×";
    removeButton.setAttribute("aria-label", "Quitar producto del pedido");
    removeCell.append(removeButton);
    row.append(productCell, quantityCell, priceCell, subtotalCell, removeCell);

    const updateOrderTotals = () => {
        const price = Number(productSelect.selectedOptions[0]?.dataset.price) || 0;
        const quantity = Number(quantityInput.value) || 0;
        priceCell.textContent = supplierOrdersCurrency.format(price);
        subtotalCell.textContent = supplierOrdersCurrency.format(price * quantity);
        if (supplierOrderTotal) {
            const orderTotal = Array.from(supplierOrderItems.rows).reduce((sum, itemRow) => {
                const itemPrice = Number(itemRow.querySelector("select")?.selectedOptions[0]?.dataset.price) || 0;
                const itemQuantity = Number(itemRow.querySelector('input[type="number"]')?.value) || 0;
                return sum + itemPrice * itemQuantity;
            }, 0);
            supplierOrderTotal.textContent = supplierOrdersCurrency.format(orderTotal);
        }
    };

    productSelect.addEventListener("change", updateOrderTotals);
    quantityInput.addEventListener("input", updateOrderTotals);
    removeButton.addEventListener("click", () => {
        if (supplierOrderItems.rows.length === 1) {
            productSelect.value = "";
            quantityInput.value = "1";
            updateOrderTotals();
            return;
        }
        row.remove();
        updateOrderTotals();
    });
    supplierOrderItems.append(row);
    updateOrderTotals();
};

supplierOrderModal?.addEventListener("show.bs.modal", () => {
    supplierOrderForm?.reset();
    supplierOrderItems?.replaceChildren();
    refreshSupplierSelect();
    if (supplierSelect) supplierSelect.disabled = supplierSelect.options.length <= 1;
    if (supplierOrderDeliveryDate) {
        const today = new Date();
        const localToday = new Date(today.getTime() - today.getTimezoneOffset() * 60000);
        supplierOrderDeliveryDate.min = localToday.toISOString().slice(0, 10);
    }
    createSupplierOrderItemRow();
});

supplierSelect?.addEventListener("change", () => {
    supplierOrderItems?.replaceChildren();
    createSupplierOrderItemRow();
});

document.querySelector("#pedidoAgregarArticulo")?.addEventListener("click", createSupplierOrderItemRow);

supplierOrderForm?.addEventListener("submit", event => {
    event.preventDefault();
    if (!supplierOrderForm.reportValidity()) return;

    const selectedProvider = supplierSelect.selectedOptions[0];
    const providerId = supplierSelect.value;
    const items = Array.from(supplierOrderItems.rows).map(row => {
        const product = row.querySelector("select").selectedOptions[0];
        const quantity = Number(row.querySelector('input[type="number"]').value);
        const unitPrice = Number(product.dataset.price);
        return {
            name: product.value,
            quantity,
            unitPrice,
            subtotal: quantity * unitPrice
        };
    });
    if (items.some(item => !item.name || !Number.isInteger(item.quantity) || item.quantity < 1)) {
        window.alert("Revisa los productos y las cantidades del pedido.");
        return;
    }

    const now = new Date();
    const localDate = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
    const orderNumber = supplierOrders.reduce((max, order) => {
        const currentNumber = Number(order.id.replace(/\D/g, "")) || 0;
        return Math.max(max, currentNumber);
    }, 0) + 1;
    const order = {
        id: `PED-${String(orderNumber).padStart(4, "0")}`,
        date: localDate,
        deliveryDate: supplierOrderDeliveryDate.value,
        providerId,
        provider: selectedProvider.dataset.providerName,
        items,
        total: items.reduce((sum, item) => sum + item.subtotal, 0),
        status: "Pendiente",
        notes: supplierOrderNotes.value.trim()
    };
    if (!persistSupplierOrders([...supplierOrders, order])) return;

    recordReportAction(`Creó el pedido ${order.id} para ${order.provider}`);
    supplierOrderForm.reset();
    bootstrap.Modal.getOrCreateInstance(supplierOrderModal).hide();
});

supplierOrdersRows?.addEventListener("change", event => {
    const statusSelect = event.target.closest("[data-pedido-estado]");
    if (!statusSelect) return;

    const order = supplierOrders.find(item => item.id === statusSelect.dataset.pedidoEstado);
    if (!order || !supplierOrdersStatus.includes(statusSelect.value) || order.status === statusSelect.value) return;
    const previousStatus = order.status;
    const nextOrders = supplierOrders.map(item => item.id === order.id
        ? { ...item, status: statusSelect.value }
        : item);
    if (persistSupplierOrders(nextOrders)) {
        recordReportAction(`Actualizó el pedido ${order.id}: ${previousStatus} a ${statusSelect.value}`);
    }
});

supplierOrdersRows?.addEventListener("click", event => {
    const deleteButton = event.target.closest("[data-pedido-eliminar]");
    if (!deleteButton) return;

    const order = supplierOrders.find(item => item.id === deleteButton.dataset.pedidoEliminar);
    if (!order || !window.confirm(`¿Eliminar el pedido ${order.id}?`)) return;
    if (persistSupplierOrders(supplierOrders.filter(item => item.id !== order.id))) {
        recordReportAction(`Eliminó el pedido ${order.id} de ${order.provider}`);
    }
});

renderSupplierOrders();

if (providerForm && providerModalElement) {
    providerForm.addEventListener("submit", event => {
        event.preventDefault();
        if (!providerForm.reportValidity()) return;

        const providerValues = Array.from(new FormData(providerForm).values())
            .map(value => String(value).trim());
        const nextProviderId = nextTableId(providersDataTable);

        providersDataTable.row.add([nextProviderId, ...providerValues, null]).draw();
        recordReportAction(`Agregó un proveedor: ${providerValues[0]}`);
        providerForm.reset();
        bootstrap.Modal.getOrCreateInstance(providerModalElement).hide();
    });
}

const editRowForm = document.querySelector("#formEditarFila");
const editRowFields = document.querySelector("#camposEditarFila");
const editRowModalElement = document.querySelector("#modalEditarFila");
let rowBeingEdited = null;

const getTableRowData = tableRow => {
    const table = tableRow.closest("table");
    if ($.fn.dataTable.isDataTable(table)) {
        return $(table).DataTable().row(tableRow);
    }
    return null;
};

const persistAppointments = nextAppointments => {
    try {
        localStorage.setItem(appointmentStorageKey, JSON.stringify(nextAppointments));
    } catch (error) {
        console.error("No se pudieron guardar los cambios de las citas.", error);
        window.alert("No se pudieron guardar los cambios de la cita. Inténtalo de nuevo.");
        return false;
    }

    appointments.splice(0, appointments.length, ...nextAppointments);
    return true;
};

const refreshAppointmentCalendar = date => {
    const selectedMonth = new Date(`${date}T00:00:00`);
    if (renderCalendarForAppointment && !Number.isNaN(selectedMonth.getTime())) {
        renderCalendarForAppointment(selectedMonth);
    }
};

document.addEventListener("click", event => {
    const actionButton = event.target.closest("[data-table-action]");
    if (!actionButton) return;

    const tableRow = actionButton.closest("tr");
    const table = actionButton.closest("table");
    if (!tableRow || !table) return;

    event.preventDefault();
    event.stopPropagation();
    const dataTableRow = getTableRowData(tableRow);
    const rowData = dataTableRow ? dataTableRow.data() : null;

    if (actionButton.dataset.tableAction === "delete") {
        if (!window.confirm("¿Seguro que quieres eliminar este registro?")) return;

        const deletedRecordActions = {
            datatable_usuarios: `Eliminó un cliente: ${rowData?.[2] || tableRow.cells[2]?.textContent.trim() || ""}`,
            datatable_mascotas: `Eliminó una mascota: ${rowData?.[1] || tableRow.cells[1]?.textContent.trim() || ""}`,
            datatable_productos: `Eliminó un producto: ${rowData?.[1] || tableRow.cells[1]?.textContent.trim() || ""}`,
            datatable_proveedores: `Eliminó un proveedor: ${rowData?.[1] || tableRow.cells[1]?.textContent.trim() || ""}`,
            datatable_citas: `Eliminó una cita: ${rowData?.[0] || tableRow.cells[0]?.textContent.trim() || ""}`
        };

        if (table.id === "datatable_citas" && rowData) {
            const nextAppointments = appointments.filter(
                appointment => String(appointment.id) !== String(rowData[0])
            );
            if (!persistAppointments(nextAppointments)) return;
            dataTableRow.remove().draw(false);
            refreshAppointmentCalendar(nextAppointments[0]?.date || new Date().toISOString().slice(0, 10));
            recordReportAction(deletedRecordActions[table.id]);
            return;
        }

        if (dataTableRow) {
            dataTableRow.remove().draw(false);
        } else {
            tableRow.remove();
        }
        if (deletedRecordActions[table.id]) {
            recordReportAction(deletedRecordActions[table.id]);
        }
        return;
    }

    if (actionButton.dataset.tableAction !== "edit") return;

    const headers = Array.from(table.querySelectorAll("thead th"));
    const values = rowData || Array.from(tableRow.cells, cell => cell.textContent.trim());
    const actionColumnIndex = headers.length - 1;
    const editableColumns = headers
        .map((header, index) => ({ index, label: header.textContent.trim() }))
        .filter(column => column.label && column.index !== actionColumnIndex && column.index < values.length);

    if (!editableColumns.length) return;
    rowBeingEdited = { table, tableRow, dataTableRow, rowData: values, editableColumns };
    editRowFields.replaceChildren();

    editableColumns.forEach(column => {
        const wrapper = document.createElement("div");
        wrapper.className = "col-12";
        const label = document.createElement("label");
        const input = document.createElement("input");
        const inputId = `editarFilaCampo${column.index}`;

        label.className = "form-label";
        label.htmlFor = inputId;
        label.textContent = column.label;
        input.className = "form-control";
        input.id = inputId;
        input.name = String(column.index);
        input.type = /fecha/i.test(column.label) ? "date" : (/hora/i.test(column.label) ? "time" : "text");
        input.value = String(values[column.index] ?? "").trim();
        wrapper.append(label, input);
        editRowFields.append(wrapper);
    });

    bootstrap.Modal.getOrCreateInstance(editRowModalElement).show();
});

editRowForm?.addEventListener("submit", event => {
    event.preventDefault();
    if (!rowBeingEdited) return;

    const { table, tableRow, dataTableRow, rowData, editableColumns } = rowBeingEdited;
    const nextRowData = [...rowData];
    editableColumns.forEach(column => {
        const input = editRowForm.elements.namedItem(String(column.index));
        nextRowData[column.index] = input.value.trim();
    });

    if (table.id === "datatable_citas") {
        const appointmentIndex = appointments.findIndex(
            appointment => String(appointment.id) === String(rowData[0])
        );
        if (appointmentIndex < 0) {
            console.error("No se encontró la cita que se intentó modificar.");
            window.alert("No se encontró la cita. Actualiza la página e inténtalo de nuevo.");
            return;
        }

        const originalAppointment = appointments[appointmentIndex];
        const updatedAppointment = {
            ...originalAppointment,
            date: nextRowData[1],
            time: nextRowData[2],
            reason: nextRowData[3] === "—" ? "" : nextRowData[3],
            status: nextRowData[4],
            serviceId: nextRowData[5] === "—" ? "" : nextRowData[5],
            service: nextRowData[5] === "—" ? "" : nextRowData[5],
            ci: nextRowData[6] === "—" ? "" : nextRowData[6],
            client: nextRowData[6] === "—" ? "" : nextRowData[6]
        };
        const nextAppointments = appointments.map((appointment, index) =>
            index === appointmentIndex ? updatedAppointment : appointment
        );
        if (!persistAppointments(nextAppointments)) return;
        refreshAppointmentCalendar(updatedAppointment.date);
    }

    if (dataTableRow) {
        dataTableRow.data(nextRowData).draw(false);
    } else {
        editableColumns.forEach(column => {
            tableRow.cells[column.index].textContent = nextRowData[column.index];
        });
    }

    bootstrap.Modal.getOrCreateInstance(editRowModalElement).hide();
});

editRowModalElement?.addEventListener("hidden.bs.modal", () => {
    editRowForm.reset();
    editRowFields.replaceChildren();
    rowBeingEdited = null;
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
        if (targetId === "servicios") {
            showServicesOverview();
        }
        if (targetId === "analiticas") {
            renderAnalytics();
        }

        if (window.innerWidth <= 786) {
            sideMenu.style.display = "none";
            sideMenu.classList.remove("mobile-open");
        }
    });

});

const showServicesOverview = () => {
    serviceOverview?.classList.remove("d-none");
    serviceDetail?.classList.add("d-none");
    serviceCards.forEach(card => card.setAttribute("aria-expanded", "false"));
};

serviceCards.forEach(card => {
    const openServiceDetail = () => {
        const title = card.querySelector(".left h3")?.textContent?.trim();
        const quantity = card.querySelector(".left h1")?.textContent?.trim();
        const indicator = card.querySelector(".number")?.textContent?.trim();
        const status = card.querySelector("small")?.textContent?.trim();

        if (!title || !quantity || !indicator || !status) {
            console.error("No se pudo abrir el detalle del panel de servicio: faltan datos.");
            return;
        }

        serviceDetailTitle.textContent = title;
        serviceDetailQuantity.textContent = quantity;
        serviceDetailIndicator.textContent = indicator;
        serviceDetailStatus.textContent = status;
        serviceOverview.classList.add("d-none");
        serviceDetail.classList.remove("d-none");
        selectedServiceCard = card;
        serviceCards.forEach(serviceCard => {
            serviceCard.setAttribute("aria-expanded", String(serviceCard === card));
        });
        serviceBackButton.focus();
    };

    card.addEventListener("click", openServiceDetail);
    card.addEventListener("keydown", event => {
        if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            openServiceDetail();
        }
    });
});

serviceBackButton?.addEventListener("click", () => {
    showServicesOverview();
    selectedServiceCard?.focus();
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
const clienteMascotaModal = clienteMascotaModalElement
    ? bootstrap.Modal.getOrCreateInstance(clienteMascotaModalElement)
    : null;
const formMascotaCliente = document.querySelector("#formMascotaCliente");
let mascotasClienteConfirmadas = false;

if (petFormCheckboxes.length && clienteMascotaModalElement) {
    let parentClientModal = null;

    clienteMascotaModalElement.addEventListener("show.bs.modal", () => {
        parentClientModal = document.querySelector(".modal.show:not(#modalMascotaCliente)");
        if (parentClientModal) {
            clienteMascotaModalElement.style.zIndex = "1070";
        }
    });

    clienteMascotaModalElement.addEventListener("shown.bs.modal", () => {
        if (!parentClientModal) return;

        const backdrops = document.querySelectorAll(".modal-backdrop");
        const childBackdrop = backdrops[backdrops.length - 1];
        if (childBackdrop) childBackdrop.style.zIndex = "1060";
    });

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
        clienteMascotaModalElement.style.zIndex = "";
        document.querySelectorAll(".modal-backdrop").forEach(backdrop => {
            backdrop.style.zIndex = "";
        });
        parentClientModal = null;
    });

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