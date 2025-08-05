@extends('layouts.app')

@section('title', 'Calendario de Rentas')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    #calendar {
        max-width: 100%;
        margin: 0 auto;
    }
    .sidebar {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 1rem;
        border-right: 1px solid #ddd;
    }
    .sidebar .btn {
        margin-bottom: 10px;
        width: 100%;
        text-align: left;
    }
    .sidebar .btn i {
        margin-right: 8px;
    }
</style>
@endpush

@section('content')
<div class="row g-0">
    <div class="col-md-3 sidebar">
        <img src="{{ asset('logo1.png') }}" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
        <h5 class="mb-3">Acciones</h5>
        <a href="{{ route('rentas.crear') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nueva Renta</a>
        <a href="{{ route('rentas.index') }}" class="btn btn-success"><i class="bi bi-list"></i> Listado de Rentas</a>
        <a href="{{ route('clientes.index') }}" class="btn btn-info"><i class="bi bi-person"></i> Clientes</a>
        <a href="{{ route('productos.index') }}" class="btn btn-warning"><i class="bi bi-box"></i> Productos</a>
        <hr>
        <a href="{{ route('reportes.diario') }}" class="btn btn-secondary"><i class="bi bi-clipboard-data"></i> Reporte Diario</a>
        <a href="{{ route('reportes.semanal') }}" class="btn btn-secondary"><i class="bi bi-calendar-week"></i> Reporte Semanal</a>
        <a href="{{ route('reportes.mensual') }}" class="btn btn-secondary"><i class="bi bi-calendar-month"></i> Reporte Mensual</a>
    </div>
    
    <div class="col-md-9 p-3">
        {{-- Cuadros resumen activos --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-success text-success shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-1">Rentas Hoy</h6>
                        <span class="display-6 fw-bold">{{ $rentasHoy }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info text-info shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-1">Rentas Semana</h6>
                        <span class="display-6 fw-bold">{{ $rentasSemana }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary text-primary shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-1">Rentas Mes</h6>
                        <span class="display-6 fw-bold">{{ $rentasMes }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger text-danger shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-1">Atrasadas</h6>
                        <span class="display-6 fw-bold">{{ $rentasAtrasadas }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <h3>Calendario de Rentas</h3>
        <div id="calendar"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: {
            url: '{{ route("calendario.eventos") }}',
            method: 'GET',
            extraParams: {
                ocultarDevueltos: true
            }
        },
        eventClick: function(info) {
            window.location.href = '/rentas/' + info.event.extendedProps.renta_id;
        },
        eventDidMount: function(info) {
            if (info.event.extendedProps.estado === 'abonado') {
                info.el.style.backgroundColor = '#ffc107'; // Amarillo
                info.el.style.borderColor = '#ffc107';
            } else if (info.event.extendedProps.estado === 'pagado') {
                info.el.style.backgroundColor = '#28a745'; // Verde
                info.el.style.borderColor = '#28a745';
            } else if (info.event.extendedProps.estado === 'atrasado') {
                info.el.style.backgroundColor = '#dc3545'; // Rojo
                info.el.style.borderColor = '#dc3545';
            } else {
                info.el.style.backgroundColor = '#6c757d'; // Gris para otros
                info.el.style.borderColor = '#6c757d';
            }
        }
    });
    calendar.render();
});
</script>
@endpush
