<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario KAT0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --verde-oscuro: #4d7e68;
            --verde-medio: #7ea88d;
            --verde-claro: #dfeee6;
            --verde-suave: #edf7f0;
            --blanco: #ffffff;
            --gris: #53646d;
            --negro: #1d2a22;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: linear-gradient(135deg, var(--verde-suave) 0%, #f7fbf8 100%);
        }

        .hero {
            background-image: url("Veterinaria.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            position: relative;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(29, 42, 34, 0.55);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.96);
            color: #222;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.22);
        }

        .btn-kat {
            background: linear-gradient(135deg, var(--verde-medio) 0%, var(--verde-oscuro) 100%);
            border: none;
            border-radius: 12px;
        }

        .btn-kat:hover {
            background: linear-gradient(135deg, #6a9d82 0%, #3d6958 100%);
        }

        .usuario-nombre {
            background: #ffffff;
            color: var(--verde-oscuro);
            border: 1px solid rgba(77, 126, 104, 0.25);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="KAT0 (Negro).png" alt="Logo KAT" width="60" class="me-2">
                <span class="fw-bold fs-4"></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#menuPrincipal" aria-controls="menuPrincipal"
                aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tienda Online</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="submenu" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Agendar Cita
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="submenu">
                            <li><a class="dropdown-item" href="#">Consulta General</a></li>
                            <li><a class="dropdown-item" href="#">Especialista</a></li>
                            <li><a class="dropdown-item" href="#">Urgencias</a></li>
                        </ul>
                    </li>
                </ul>

                <button class="btn usuario-nombre ms-auto">
                    <?php echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Iniciar Sesión'; ?>
                </button>
            </div>
        </div>
    </nav>

    <section class="hero d-flex align-items-center justify-content-center py-5">
        <div class="container hero-content">
            <div class="row align-items-center g-4">
                <div class="col-lg-6 text-center text-lg-start">
                    <h1 class="display-4 fw-bold">Completa tu solicitud</h1>
                    <p class="lead mt-3">
                        Estamos listos para atenderte. Rellena el formulario y te contactaremos pronto.
                    </p>
                </div>

                <div class="col-lg-6">
                    <form class="p-4 form-card">
                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-semibold">Nombre completo</label>
                            <input type="text" class="form-control" id="nombre" placeholder="Tu nombre" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" placeholder="correo@ejemplo.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" placeholder="123456789">
                        </div>

                        <div class="mb-3">
                            <label for="servicio" class="form-label fw-semibold">Servicio</label>
                            <select class="form-select" id="servicio">
                                <option selected>Consulta general</option>
                                <option>Especialista</option>
                                <option>Urgencias</option>
                                <option>Otro</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="fecha" class="form-label fw-semibold">Fecha preferida</label>
                            <input type="date" class="form-control" id="fecha">
                        </div>

                        <div class="mb-3">
                            <label for="mensaje" class="form-label fw-semibold">Mensaje</label>
                            <textarea class="form-control" id="mensaje" rows="4" placeholder="Cuéntanos cómo podemos ayudarte"></textarea>
                        </div>

                        <button type="submit" class="btn btn-kat text-white w-100">Enviar solicitud</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
