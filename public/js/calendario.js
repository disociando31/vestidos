document.addEventListener('DOMContentLoaded', function () {
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
            url: '/calendario/eventos',  // ✅ Asegúrate que esta ruta esté configurada en routes/web.php
            method: 'GET',
            extraParams: {
                ocultarDevueltos: true  // 🔸 Aquí le enviamos el parámetro para ocultar devueltos
            }
        },
        eventClick: function (info) {
            window.location.href = '/rentas/' + info.event.extendedProps.renta_id;
        },
        eventDidMount: function (info) {
            if (info.event.extendedProps.estado === 'abonado' || info.event.extendedProps.estado === 'parcial') {
                info.el.style.backgroundColor = '#ffc107'; // Amarillo
                info.el.style.borderColor = '#ffc107';
            } else if (info.event.extendedProps.estado === 'pagado') {
                info.el.style.backgroundColor = '#28a745'; // Verde
                info.el.style.borderColor = '#28a745';
            } else if (info.event.extendedProps.estado === 'devuelto') {
                info.el.style.backgroundColor = '#0dcaf0'; // Azul claro
                info.el.style.borderColor = '#0dcaf0';
            } else {
                info.el.style.backgroundColor = '#dc3545'; // Rojo (pendiente o atrasado)
                info.el.style.borderColor = '#dc3545';
            }
        }
    });

    calendar.render();
});