<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alquiler de Vestidos</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #d63384;
        }
        .navbar-brand, .nav-link, .nav-item {
            color: #fff !important;
        }
        .btn-custom {
            margin-right: 10px;
        }
        #calendar {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Boutique de Vestidos</a>
            <div>
                <a href="{{ route('rentas.index') }}" class="btn btn-light btn-custom">Rentas</a>
                <a href="{{ route('clientes.index') }}" class="btn btn-light btn-custom">Clientes</a>
                <a href="{{ route('productos.index') }}" class="btn btn-light btn-custom">Productos</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h2>Calendario de Alquileres</h2>
            <div>
                <a href="{{ route('rentas.crear') }}" class="btn btn-success btn-custom">Nueva Renta</a>
                <a href="{{ route('rentas.index') }}" class="btn btn-primary btn-custom">Buscar Renta</a>
            </div>
        </div>

        <div id="calendar"></div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.8/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
                },
                events: '/calendario/eventos', // Esta ruta debe estar activa
                eventClick: function(info) {
                    alert('Renta: ' + info.event.extendedProps.cliente + '\n' +
                        'Productos: ' + info.event.extendedProps.productos + '\n' +
                        'Estado: ' + info.event.extendedProps.estado + '\n' +
                        'Total: $' + info.event.extendedProps.total + '\n' +
                        'Pagado: $' + info.event.extendedProps.pagado + '\n' +
                        'Saldo: $' + info.event.extendedProps.saldo);
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>
