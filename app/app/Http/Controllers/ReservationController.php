<?php

namespace App\Http\Controllers;

use App\Mail\ReservationConfirmationMail;
use App\Mail\ReservationNotificationMail;
use App\Models\EventType;
use App\Models\Menu;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function create()
    {
        $eventTypes = EventType::where('active', true)->orderBy('sort_order')->get();
        $rooms = Room::where('active', true)->get();

        return view('reservation.create', compact('eventTypes', 'rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|max:255',
            'phone'         => 'nullable|string|max:30',
            'event_type_id' => 'required|exists:event_types,id',
            'room_id'       => 'nullable|exists:rooms,id',
            'menu_id'       => 'nullable|exists:menus,id',
            'event_date'    => 'required|date|after_or_equal:today',
            'event_time'    => 'required|date_format:H:i',
            'duration_hours'=> 'required|numeric|min:1',
            'guest_count'   => 'required|integer|min:1',
            'notes'         => 'nullable|string|max:2000',
        ], [
            'first_name.required'    => 'Imię jest wymagane.',
            'last_name.required'     => 'Nazwisko jest wymagane.',
            'email.required'         => 'Adres e-mail jest wymagany.',
            'email.email'            => 'Podaj prawidłowy adres e-mail.',
            'event_type_id.required' => 'Wybierz rodzaj imprezy.',
            'event_date.required'    => 'Data imprezy jest wymagana.',
            'event_date.after_or_equal' => 'Data imprezy nie może być w przeszłości.',
            'event_time.required'    => 'Godzina rozpoczęcia jest wymagana.',
            'duration_hours.required'=> 'Czas trwania jest wymagany.',
            'guest_count.required'   => 'Liczba gości jest wymagana.',
            'guest_count.min'        => 'Minimalna liczba gości to 1.',
        ]);

        $reservation = Reservation::create($validated);

        // Mail do klienta
        Mail::to($reservation->email)->send(new ReservationConfirmationMail($reservation));

        // Mail do admina
        $adminEmail = config('mail.admin_email');
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new ReservationNotificationMail($reservation));
        }

        return redirect()->route('reservation.success', ['ref' => $reservation->reference]);
    }

    public function success(Request $request)
    {
        $reservation = Reservation::where('reference', $request->ref)->firstOrFail();
        return view('reservation.success', compact('reservation'));
    }

    public function blockedDates(Request $request): JsonResponse
    {
        $roomId = $request->query('room_id');
        if (! $roomId) {
            return response()->json([]);
        }

        $room = Room::findOrFail($roomId);
        return response()->json($room->getBookedDates());
    }

    public function calendarEvents(Request $request): JsonResponse
    {
        $reservations = Reservation::with(['eventType', 'room'])
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->map(function (Reservation $r) {
                $color = match ($r->status) {
                    'new'              => '#94a3b8', // szary
                    'contacted'        => '#f59e0b', // pomarańczowy
                    'awaiting_payment' => '#3b82f6', // niebieski
                    'confirmed'        => '#22c55e', // zielony
                    'completed'        => '#8b5cf6', // fioletowy
                    default            => '#94a3b8',
                };

                $start = $r->event_date->format('Y-m-d') . 'T' . $r->event_time;
                $end   = $r->event_date->copy()
                    ->setTimeFromTimeString($r->event_time)
                    ->addHours((int) $r->duration_hours)
                    ->format('Y-m-d\TH:i');

                return [
                    'id'              => $r->id,
                    'title'           => $r->full_name . ' · ' . $r->eventType->name,
                    'start'           => $start,
                    'end'             => $end,
                    'color'           => $color,
                    'extendedProps'   => [
                        'status'      => $r->status_label,
                        'reference'   => $r->reference,
                        'guests'      => $r->guest_count,
                        'room'        => $r->room?->name ?? '—',
                        'email'       => $r->email,
                        'phone'       => $r->phone ?? '—',
                        'edit_url'    => '/admin/reservations/' . $r->id . '/edit',
                    ],
                ];
            });

        return response()->json($reservations);
    }

    public function menus(Request $request): JsonResponse
    {
        $eventTypeId = $request->query('event_type_id');
        if (! $eventTypeId) {
            return response()->json([]);
        }

        $menus = Menu::where('event_type_id', $eventTypeId)
            ->where('active', true)
            ->orderBy('sort_order')
            ->with('courses')
            ->get()
            ->map(fn (Menu $menu) => [
                'id'               => $menu->id,
                'name'             => $menu->name,
                'description'      => $menu->description,
                'price_per_person' => $menu->price_per_person,
                'courses'          => $menu->courses->map(fn ($c) => [
                    'type'        => $c->type,
                    'type_label'  => \App\Models\MenuCourse::typeLabel($c->type),
                    'name'        => $c->name,
                    'description' => $c->description,
                ]),
            ]);

        return response()->json($menus);
    }
}
