<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TicketOrder;
use App\Models\Event;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $unreadNotifications = Auth::user()->unreadNotifications;

        $paidStatus = 'paid';
        $pendingStatus = 'pending';

        $startOfCurrentMonth = Carbon::now()->startOfMonth();
        $startOfPreviousMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfPreviousMonth = Carbon::now()->subMonth()->endOfMonth();

        $totalTicketsCurrent = TicketOrder::where('status', $paidStatus)
            ->where('updated_at', '>=', $startOfCurrentMonth)
            ->sum('quantity');
        $totalTicketsPrevious = TicketOrder::where('status', $paidStatus)
            ->whereBetween('updated_at', [$startOfPreviousMonth, $endOfPreviousMonth])
            ->sum('quantity');
        $totalTickets = TicketOrder::where('status', $paidStatus)->sum('quantity');
        $ticketChange = $this->calculatePercentageChange($totalTicketsCurrent, $totalTicketsPrevious);

        $totalRevenueCurrent = TicketOrder::where('status', $paidStatus)
            ->where('updated_at', '>=', $startOfCurrentMonth)
            ->sum('total_price');
        $totalRevenuePrevious = TicketOrder::where('status', $paidStatus)
            ->whereBetween('updated_at', [$startOfPreviousMonth, $endOfPreviousMonth])
            ->sum('total_price');
        $totalRevenue = TicketOrder::where('status', $paidStatus)->sum('total_price');
        $revenueChange = $this->calculatePercentageChange($totalRevenueCurrent, $totalRevenuePrevious);

        $pendingOrders = TicketOrder::where('status', $pendingStatus)->count();
        $pendingOrdersPrevious = TicketOrder::where('status', $pendingStatus)
            ->whereBetween('created_at', [$startOfPreviousMonth, $endOfPreviousMonth])
            ->count();
        $pendingChange = $this->calculatePercentageChange($pendingOrdersPrevious, $pendingOrders);
        $pendingChange['status'] = $pendingChange['percentage'] >= 0 ? 'negative' : 'positive';

        $attendeesCount = User::where('role', 'pengguna')->count();
        $attendeesCurrent = User::where('role', 'pengguna')
            ->where('created_at', '>=', $startOfCurrentMonth)
            ->count();
        $attendeesPrevious = User::where('role', 'pengguna')
            ->whereBetween('created_at', [$startOfPreviousMonth, $endOfPreviousMonth])
            ->count();
        $attendeesChange = $this->calculatePercentageChange($attendeesCurrent, $attendeesPrevious);

        $stats = [
            'total_tickets' => $totalTickets,
            'total_revenue' => $totalRevenue,
            'pending_tickets' => $pendingOrders,
            'events_count' => Event::where('status', 'published')->count(),
            'attendees_count' => $attendeesCount,
            'change_ticket' => $ticketChange['percentage'],
            'change_ticket_status' => $ticketChange['status'],
            'change_revenue' => $revenueChange['percentage'],
            'change_revenue_status' => $revenueChange['status'],
            'change_pending' => $pendingChange['percentage'],
            'change_pending_status' => $pendingChange['status'],
            'change_attendees' => $attendeesChange['percentage'],
            'change_attendees_status' => $attendeesChange['status'],
        ];

        $recentTicketOrders = TicketOrder::with(['event', 'user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'event' => $order->event->nama_event ?? 'Event Dihapus',
                    'customer' => $order->user->name ?? 'Pengguna Dihapus',
                    'type' => $order->quantity . ' Tiket',
                    'price' => $order->total_price,
                    'status' => $order->status,
                    'date' => $order->created_at->format('Y-m-d'),
                ];
            })->toArray();

        $topEventsData = $this->getTopEventsData($paidStatus);
        $monthlySalesData = $this->getMonthlySalesData($paidStatus);

        $dashboardData = [
            'stats' => $stats,
            'recent_tickets' => $recentTicketOrders,
            'top_events' => $topEventsData,
            'monthly_sales' => $monthlySalesData,
        ];

        return view('admin.pages.dashboard', compact('dashboardData', 'unreadNotifications'));
    }

    protected function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            $percentage = $current > 0 ? 100.0 : 0.0;
        } else {
            $percentage = (($current - $previous) / $previous) * 100;
        }

        $status = $percentage >= 0 ? 'positive' : 'negative';

        return [
            'percentage' => abs(round($percentage, 1)),
            'status' => $status
        ];
    }

    protected function getMonthlySalesData(string $paidStatus)
    {
        $months = [];
        $sales = [];
        $now = Carbon::now();

        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $monthName = $date->shortMonthName;
            $year = $date->year;

            $revenue = TicketOrder::where('status', $paidStatus)
                ->whereYear('updated_at', $year)
                ->whereMonth('updated_at', $date->month)
                ->sum('total_price');

            $months[] = $monthName;
            $sales[] = (int)$revenue;
        }

        return [
            'labels' => $months,
            'data' => $sales,
        ];
    }

    protected function getTopEventsData(string $paidStatus)
    {
        $topEvents = Event::leftJoin('ticket_orders', 'events.id', '=', 'ticket_orders.event_id')
            ->select(
                'events.nama_event',
                DB::raw('SUM(CASE WHEN ticket_orders.status = "'.$paidStatus.'" THEN ticket_orders.quantity ELSE 0 END) as tickets_sold_count'),
                DB::raw('SUM(CASE WHEN ticket_orders.status = "'.$paidStatus.'" THEN ticket_orders.total_price ELSE 0 END) as total_revenue')
            )
            ->groupBy('events.id', 'events.nama_event')
            ->orderByDesc('tickets_sold_count')
            ->limit(5)
            ->get();

        return $topEvents->map(function ($event) {
            return [
                'name' => $event->nama_event,
                'tickets_sold' => (int)$event->tickets_sold_count,
                'revenue' => (int)$event->total_revenue,
            ];
        })->toArray();
    }
}
