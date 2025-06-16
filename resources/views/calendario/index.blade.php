@extends('layouts.app')

@section('title', 'Calendario de Rentas')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Calendario de Rentas</h5>
    </div>
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
#calendar {
    max-width: 100%;
    margin: 0 auto;
}
.fc-event {
    cursor: pointer;
}
</style>
@endpush

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
            method: 'GET'
        },
        eventClick: function(info) {
            window.location.href = '/rentas/' + info.event.extendedProps.renta_id;
        }
    });
    calendar.render();
});
</script>
@endpush
@endsection