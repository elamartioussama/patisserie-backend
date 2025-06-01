<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;

class ComplaintController extends Controller
{
    public function general()
    {
        $usersCount = User::count();
    $productsCount = Product::count();
    $ordersCount = Order::count();
    $ordersPending = Order::where('status', 'en attente')->count();
    $ordersReady = Order::where('status', 'prête')->count();

    $revenueToday = Order::whereDate('created_at', now()->toDateString())->sum('total');
    $revenueWeek = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total');
    $revenueMonth = Order::whereMonth('created_at', now()->month)->sum('total');

    // Revenu par jour de la semaine
    $revenuePerDay = [];
    for ($i = 0; $i < 7; $i++) {
        $day = now()->startOfWeek()->addDays($i);
        $revenuePerDay[] = [
            'day' => $day->translatedFormat('D'), // Lun, Mar...
            'revenue' => Order::whereDate('created_at', $day->toDateString())->sum('total'),
        ];
    }

    // Nombre commandes par statut
    $ordersStatus = Order::select('status', \DB::raw('count(*) as count'))
        ->groupBy('status')->get();

    return response()->json([
        'users' => $usersCount,
        'products' => $productsCount,
        'orders' => $ordersCount,
        'orders_pending' => $ordersPending,
        'orders_ready' => $ordersReady,
        'revenue_today' => $revenueToday,
        'revenue_week' => $revenueWeek,
        'revenue_month' => $revenueMonth,
        'revenue_per_day' => $revenuePerDay,
        'orders_status' => $ordersStatus,
    ]);
    }
    public function stats()
    {
        $usersCount = User::count();
        $productsCount = Product::count();
        $ordersCount = Order::count();
        $ordersPending = Order::where('status', 'en_attente')->count();
        $ordersReady = Order::where('status', 'prête')->count();

        $todayRevenue = Order::whereDate('created_at', Carbon::today())->sum('total_price');
        $weekRevenue = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_price');
        $monthRevenue = Order::whereMonth('created_at', Carbon::now()->month)->sum('total_price');

        return response()->json([
            'users' => $usersCount,
            'products' => $productsCount,
            'orders' => $ordersCount,
            'orders_pending' => $ordersPending,
            'orders_ready' => $ordersReady,
            'revenue_today' => $todayRevenue,
            'revenue_week' => $weekRevenue,
            'revenue_month' => $monthRevenue,
        ]);
    }
}
