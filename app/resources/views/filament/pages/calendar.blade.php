<x-filament-panels::page>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
    <style>
        #fc-wrapper { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; }
        .dark #fc-wrapper { background: #1f2937; border-color: #374151; }
        #calendar { min-height: 680px; }
        .fc-event { cursor: pointer !important; border: none !important; padding: 1px 4px; font-size: 12px; border-radius: 4px !important; }
        .fc-event:hover { filter: brightness(0.92); }
        .fc-toolbar-title { font-size: 1.1rem !important; font-weight: 600; }
        .dark .fc { color: #e5e7eb; }
        .dark .fc-theme-standard td,
        .dark .fc-theme-standard th,
        .dark .fc-theme-standard .fc-scrollgrid { border-color: #374151; }
        .dark .fc-col-header-cell-cushion,
        .dark .fc-daygrid-day-number,
        .dark .fc-list-event-title,
        .dark .fc-list-day-text,
        .dark .fc-list-day-side-text { color: #d1d5db; }
        .dark .fc-toolbar-title { color: #f9fafb; }
        .dark .fc-button-primary { background-color: #374151 !important; border-color: #4b5563 !important; color: #f9fafb !important; }
        .dark .fc-button-primary:hover { background-color: #4b5563 !important; }
        .dark .fc-button-primary.fc-button-active,
        .dark .fc-button-primary:not(:disabled).fc-button-active { background-color: #d97706 !important; border-color: #d97706 !important; }
        .dark .fc-day-today { background: rgba(245,158,11,0.08) !important; }
        .dark .fc-list-empty { color: #9ca3af; }

        /* Modal */
        #ev-modal { display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:16px; }
        #ev-modal.open { display:flex; }
        #ev-backdrop { position:absolute; inset:0; background:rgba(0,0,0,.5); }
        #ev-card { position:relative; background:white; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.2); max-width:440px; width:100%; padding:24px; z-index:10; }
        .dark #ev-card { background:#1f2937; color:#f3f4f6; }
        #ev-card .row { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #f3f4f6; font-size:13px; }
        .dark #ev-card .row { border-color:#374151; }
        #ev-card .row:last-child { border:none; }
        #ev-card .lbl { color:#9ca3af; }
        #ev-card .val { font-weight:500; text-align:right; max-width:60%; word-break:break-word; }
        #ev-dot { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:6px; vertical-align:middle; }
    </style>

    {{-- Legenda --}}
    <div class="flex flex-wrap gap-x-5 gap-y-2 mb-4">
        @foreach([
            ['#94a3b8','Nowe'],
            ['#f59e0b','W kontakcie'],
            ['#3b82f6','Oczekuje na płatność'],
            ['#22c55e','Potwierdzone'],
            ['#8b5cf6','Zrealizowane'],
        ] as [$color, $label])
        <div class="flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-400">
            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:{{ $color }}"></span>
            {{ $label }}
        </div>
        @endforeach
    </div>

    {{-- Kalendarz --}}
    <div id="fc-wrapper">
        <div id="calendar"></div>
    </div>

    {{-- Modal --}}
    <div id="ev-modal">
        <div id="ev-backdrop"></div>
        <div id="ev-card">
            <button id="ev-close" style="position:absolute;top:14px;right:14px;background:none;border:none;cursor:pointer;color:#9ca3af;font-size:20px;line-height:1">✕</button>
            <h3 id="ev-title" style="font-size:15px;font-weight:600;margin:0 24px 16px 0;line-height:1.4"></h3>
            <div id="ev-body"></div>
            <a id="ev-link" href="#"
               style="display:inline-flex;align-items:center;gap:6px;margin-top:16px;background:#f59e0b;color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none">
                Edytuj rezerwację →
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
    (function () {
        const modal   = document.getElementById('ev-modal');
        const title   = document.getElementById('ev-title');
        const body    = document.getElementById('ev-body');
        const link    = document.getElementById('ev-link');

        document.getElementById('ev-close').onclick    = () => modal.classList.remove('open');
        document.getElementById('ev-backdrop').onclick = () => modal.classList.remove('open');

        function fmt(d) {
            return d ? d.toLocaleString('pl-PL', {
                day:'2-digit', month:'2-digit', year:'numeric',
                hour:'2-digit', minute:'2-digit'
            }) : '—';
        }

        function row(label, value) {
            return `<div class="row"><span class="lbl">${label}</span><span class="val">${value}</span></div>`;
        }

        const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            locale: 'pl',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,timeGridWeek,listMonth',
            },
            buttonText: { today:'Dziś', month:'Miesiąc', week:'Tydzień', list:'Lista' },
            height: 'auto',
            nowIndicator: true,
            dayMaxEvents: 4,
            events: '/api/calendar-events',
            eventClick: function (info) {
                const p = info.event.extendedProps;
                title.innerHTML = `<span id="ev-dot" style="background:${info.event.backgroundColor}"></span>${info.event.title}`;
                body.innerHTML =
                    row('Nr ref.',    `<span style="font-family:monospace;color:#d97706">${p.reference}</span>`) +
                    row('Status',     p.status) +
                    row('Rozpoczęcie',fmt(info.event.start)) +
                    row('Zakończenie',fmt(info.event.end)) +
                    row('Sala',       p.room) +
                    row('Gości',      p.guests) +
                    row('E-mail',     p.email) +
                    row('Telefon',    p.phone);
                link.href = p.edit_url;
                modal.classList.add('open');
            },
        });

        calendar.render();
    })();
    </script>

</x-filament-panels::page>
