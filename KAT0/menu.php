<!DOCTYPE html>
<!--
  Esta portada es HTML estático servido desde un archivo .php.
  Actualmente no ejecuta consultas ni necesita conectarse a MySQL.
  Se conserva la extensión .php porque el proyecto ya enlaza a menu.php.
-->
<html lang="es">

<head>

  <!-- Permite mostrar correctamente tildes y caracteres propios del español. -->
  <meta charset="UTF-8">

  <!-- Hace que el diseño se adapte al ancho real de celulares y tabletas. -->
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <!-- Texto que aparece en la pestaña del navegador y en favoritos. -->
  <title>Veterinaria Animalada | Cuidamos de su bienestar</title>

  <!--
    Bootstrap aporta la grilla responsive, botones, navegación y utilidades
    como container, row, gap y d-flex. Requiere conexión a internet.
  -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >

  <!-- Biblioteca de iconos usados en textos y botones; también requiere internet. -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet"
  >

  <style>
    /* Fuentes de Google: DM Sans para texto general y Manrope para títulos. */
    @import url("https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap");

    /*
      Colores reutilizables del sitio. Al cambiar una variable aquí,
      se actualizan todos los componentes que la usan más abajo.
    */
    :root {
      --color-verde-oscuro: #173d32;
      --color-verde-principal: #2e7057;
      --color-fondo-menta: #dcebe1;
      --color-coral-acento: #e48769;
      --color-texto-principal: #24352e;
      --color-texto-secundario: #68766f;
    }

    /* Estilos generales heredados por toda la página. */
    body {
      margin: 0;
      color: var(--color-texto-principal);
      font-family: "DM Sans", sans-serif;
    }

    /* Barra de navegación superior; la altura disminuye en pantallas pequeñas. */
    .barra {
      min-height: 78px;
      background: #fbfcfa;
    }

    /* Tamaño del logo para mantenerlo proporcionado y sin deformarlo. */
    .logo-menu {
      width: 56px;
      height: 56px;
      object-fit: contain;
    }

    /* Tipografía y color del nombre de la veterinaria en la barra. */
    .marca-texto {
      color: var(--color-verde-oscuro);
      font-family: "Manrope", sans-serif;
      font-size: 1.02rem;
      font-weight: 800;
    }

    /* Apariencia de los enlaces de navegación. */
    .nav-link {
      color: #4f6258;
      font-size: 0.92rem;
      font-weight: 600;
    }

    /* Cambio de color al pasar el puntero por un enlace. */
    .nav-link:hover {
      color: var(--color-verde-principal);
    }

    /* Estilo compartido por los botones principales que llevan al registro. */
    .btn-animalada {
      padding: 0.75rem 1.25rem;
      border: 0;
      background: var(--color-coral-acento);
      color: #fff;
      font-weight: 700;
    }

    /* El botón oscurece su fondo al pasar el puntero para indicar interacción. */
    .btn-animalada:hover {
      background: #ce6f52;
      color: #fff;
    }

    /*
      Sección principal o "hero": ocupa gran parte de la primera pantalla.
      position relative sirve de referencia para superponer foto y contenido.
    */
    .hero {
      position: relative;
      display: flex;
      min-height: min(68vh, 650px);
      align-items: center;
      overflow: hidden;
      background: var(--color-verde-oscuro);
      color: white;
    }

    /* La foto cubre el hero sin deformarse; object-position permite ajustar el recorte. */
    .hero-foto {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center 43%;
    }

    /*
      Capa oscura sobre la foto. El degradado mejora la lectura del texto,
      especialmente a la izquierda, sin ocultar por completo la imagen.
    */
    .hero::after {
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, rgba(15, 44, 35, 0.86) 0%, rgba(15, 44, 35, 0.63) 43%, rgba(15, 44, 35, 0.08) 100%);
      content: "";
    }

    /* El contenido queda por encima de la foto y del degradado gracias al z-index. */
    .hero-contenido {
      position: relative;
      z-index: 1;
      max-width: 700px;
      padding-top: 5rem;
      padding-bottom: 5rem;
    }

    /* Texto breve sobre el título principal, acompañado por un icono. */
    .hero-etiqueta {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      margin-bottom: 1.25rem;
      color: #e1f1e5;
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
    }

    /* Título principal de la portada; clamp ajusta su tamaño al ancho disponible. */
    .hero h1 {
      max-width: 650px;
      margin-bottom: 1rem;
      font-family: "Manrope", sans-serif;
      font-size: clamp(2.7rem, 6vw, 5rem);
      font-weight: 800;
      line-height: 1.06;
    }

    /* Texto descriptivo del hero, limitado para conservar líneas legibles. */
    .hero p {
      max-width: 510px;
      margin-bottom: 1.75rem;
      color: rgba(255, 255, 255, 0.88);
      font-size: 1.1rem;
      line-height: 1.7;
    }

    /* Enlace secundario del hero, diseñado para verse sobre la fotografía. */
    .btn-contorno {
      color: white;
      font-weight: 600;
    }

    /* Color del enlace secundario al pasar el puntero. */
    .btn-contorno:hover {
      color: #e1f1e5;
    }

    /* Sección de presentación, con espacio y fondo distintos al hero. */
    .servicios {
      padding-top: 5rem;
      padding-bottom: 5rem;
      background: #f4f7f3;
    }

    /* Pequeño rótulo que introduce cada sección. */
    .seccion-etiqueta {
      color: var(--color-verde-principal);
      font-size: 0.78rem;
      font-weight: 700;
      text-transform: uppercase;
    }

    /* Estilo común para los títulos de secciones inferiores. */
    .titulo-seccion {
      font-family: "Manrope", sans-serif;
      font-size: clamp(1.9rem, 3vw, 2.65rem);
      font-weight: 800;
    }

    /* Cada característica ocupa una columna de Bootstrap y no es un enlace. */
    .servicio {
      height: 100%;
      padding: 1.5rem 0.5rem 0;
      border-top: 2px solid #d6e5d9;
    }

    /* Color y tamaño de los iconos que acompañan las características. */
    .servicio-icono {
      margin-bottom: 1rem;
      color: var(--color-coral-acento);
      font-size: 1.55rem;
    }

    /* Jerarquía tipográfica de los subtítulos de cada característica. */
    .servicio h3 {
      font-family: "Manrope", sans-serif;
      font-size: 1.12rem;
      font-weight: 800;
    }

    /* Color y espaciado legible para descripciones y texto institucional. */
    .servicio p,
    .texto-nosotros {
      color: var(--color-texto-secundario);
      line-height: 1.7;
    }

    /* Bloque institucional separado visualmente de la sección anterior. */
    .nosotros {
      padding-top: 4.5rem;
      padding-bottom: 4.5rem;
      background: var(--color-fondo-menta);
    }

    /* Pie de página con la identidad de la veterinaria. */
    .pie {
      padding: 1.5rem 0;
      background: var(--color-verde-oscuro);
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.88rem;
    }

    /*
      Ajustes para teléfonos y tabletas. Bootstrap ya apila columnas;
      aquí se reducen alturas y espacios para que el contenido quepa mejor.
    */
    @media (max-width: 767.98px) {
      .barra {
        min-height: 68px;
      }

      .logo-menu {
        width: 48px;
        height: 48px;
      }

      .hero {
        min-height: 570px;
      }

      .hero::after {
        background: linear-gradient(90deg, rgba(15, 44, 35, 0.83), rgba(15, 44, 35, 0.3));
      }

      .hero-contenido {
        padding-top: 4rem;
        padding-bottom: 4rem;
      }

      .servicios {
        padding-top: 3.5rem;
        padding-bottom: 3.5rem;
      }
    }

  </style>

</head>


<body>

<!--
  NAVEGACIÓN: navbar-expand-md mantiene el menú desplegado desde tabletas
  y lo convierte en botón hamburguesa en pantallas más estrechas.
-->
<nav class="navbar navbar-expand-md barra">
  <div class="container">
    <!-- El logotipo vuelve al inicio de la portada, alt vacío porque el nombre aparece al lado. -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="#inicio" aria-label="Veterinaria Animalada, inicio">
      <img src="KAT0 (Principal).png" class="logo-menu" alt="">
      <span class="marca-texto">Veterinaria Animalada</span>
    </a>

    <!-- Bootstrap muestra este botón cuando los enlaces no caben en una sola fila. -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navegacion" aria-controls="navegacion" aria-expanded="false" aria-label="Abrir navegación">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!--
      El id navegacion coincide con data-bs-target del botón anterior.
      Los href con # desplazan a secciones de esta misma página.
    -->
    <div class="collapse navbar-collapse" id="navegacion">
      <div class="navbar-nav ms-auto align-items-md-center gap-md-3">
        <!-- Debe coincidir con id="inicio" en la sección principal. -->
        <a class="nav-link" href="#inicio">Inicio</a>
        <!-- Debe coincidir con id="servicios" en la sección de presentación. -->
        <a class="nav-link" href="#servicios">Nuestro cuidado</a>
        <!-- Debe coincidir con id="nosotros" en el bloque institucional. -->
        <a class="nav-link" href="#nosotros">Nosotros</a>
        <!-- Lleva al formulario para que una persona registrada inicie sesión. -->
        <a class="nav-link" href="login.php">Iniciar sesión</a>
        <!-- index.html contiene el formulario de creación de cuenta del proyecto. -->
        <a class="btn btn-animalada rounded-pill ms-md-2" href="index.html">Crear cuenta</a>
      </div>
    </div>
  </div>
</nav>

<main>
  <!--
    HERO: primera sección que ve el visitante. La imagen es externa;
    para usar una foto propia, cambia el src por la ruta de esa imagen.
    El texto alternativo describe la escena a personas que usan lector de pantalla.
  -->
  <section class="hero" id="inicio">
    <img class="hero-foto" src="https://images.unsplash.com/photo-1628009368231-7bb7cfcb0def?auto=format&fit=crop&w=2000&q=85" alt="Veterinaria atendiendo con cuidado a un perro">
    <!-- container limita el ancho del contenido; el hero sigue ocupando todo el ancho. -->
    <div class="container hero-contenido">
      <!-- aria-hidden evita que el lector de pantalla repita el icono decorativo. -->
      <div class="hero-etiqueta"><i class="bi bi-heart-pulse-fill" aria-hidden="true"></i> Cuidado veterinario con cariño</div>
      <!-- Se conserva un único h1 para identificar el tema principal de la página. -->
      <h1>Veterinaria Animalada</h1>
      <p>Cuidamos a tus mascotas como parte de la familia. Un espacio para acompañar su salud y bienestar en cada etapa.</p>
      <!-- Acciones de la portada: registro y desplazamiento a la siguiente sección. -->
      <div class="d-flex flex-wrap align-items-center gap-3">
        <a class="btn btn-animalada rounded-pill" href="index.html">Crear mi cuenta <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></a>
        <a class="btn btn-contorno" href="#servicios">Conocé más <i class="bi bi-chevron-down ms-1" aria-hidden="true"></i></a>
      </div>
    </div>
  </section>

  <!--
    PRESENTACIÓN: el enlace "Nuestro cuidado" del menú y el botón "Conocé más"
    llegan aquí. Las columnas usan la grilla de Bootstrap y se apilan en móvil.
  -->
  <section class="servicios" id="servicios">
    <div class="container">
      <div class="row align-items-end gy-3 mb-4 mb-lg-5">
        <div class="col-lg-7">
          <div class="seccion-etiqueta mb-2">Cerca de quienes más querés</div>
          <h2 class="titulo-seccion mb-0">El bienestar de tu mascota, en buenas manos.</h2>
        </div>
        <div class="col-lg-5">
          <p class="texto-nosotros mb-0">Cada mascota es única. Por eso, la atención empieza con escuchar y conocer a quienes forman parte de su vida.</p>
        </div>
      </div>

      <!-- Cada artículo es contenido informativo; no aparenta ser una función enlazada. -->
      <div class="row g-4">
        <div class="col-md-4">
          <article class="servicio">
            <div class="servicio-icono"><i class="bi bi-heart-pulse" aria-hidden="true"></i></div>
            <h3>Atención con cariño</h3>
            <p>Un trato paciente y cercano para que cada visita sea una experiencia más tranquila.</p>
          </article>
        </div>
        <div class="col-md-4">
          <article class="servicio">
            <div class="servicio-icono"><i class="bi bi-shield-plus" aria-hidden="true"></i></div>
            <h3>Cuidado responsable</h3>
            <p>Acompañamos a las familias con información clara para cuidar la salud de sus mascotas.</p>
          </article>
        </div>
        <div class="col-md-4">
          <article class="servicio">
            <div class="servicio-icono"><i class="bi bi-people" aria-hidden="true"></i></div>
            <h3>En familia</h3>
            <p>Creemos que el bienestar animal también se construye junto a las personas que los quieren.</p>
          </article>
        </div>
      </div>
    </div>
  </section>

  <!-- Bloque institucional; el enlace "Nosotros" del menú apunta a este id. -->
  <section class="nosotros" id="nosotros">
    <div class="container">
      <div class="row align-items-center gy-3">
        <div class="col-lg-8">
          <div class="seccion-etiqueta mb-2">Veterinaria Animalada</div>
          <h2 class="titulo-seccion mb-2">Su salud nos importa. Su confianza, también.</h2>
          <p class="texto-nosotros mb-0">Queremos que cada familia se sienta acompañada al cuidar de sus compañeros de vida.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
          <!-- Este botón reutiliza el formulario existente en lugar de inventar otra ruta. -->
          <a class="btn btn-animalada rounded-pill" href="index.html">Conocenos y registrate <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></a>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Pie breve con la marca y el mismo mensaje de cuidado de la portada. -->
<footer class="pie">
  <div class="container d-flex flex-column flex-sm-row justify-content-between gap-2">
    <span>Veterinaria Animalada</span>
    <span>Cuidamos a tus mascotas como parte de la familia.</span>
  </div>
</footer>

<!-- El bundle JavaScript habilita el menú plegable de Bootstrap en móvil. -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
