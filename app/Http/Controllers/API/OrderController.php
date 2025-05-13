<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    // ✅ Lister toutes les commandes
    public function index()
    {
        return response()->json(Order::all(), 200);
    }

    // ✅ Créer une nouvelle commande
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'total_price' => 'required|numeric|min:0',
            'status'      => 'nullable|string',
        ]);

        $order = Order::create($validated);

        return response()->json($order, 201);
    }

    // ✅ Voir une commande
    public function show($id)
    {
        $order = Order::with('user')->find($id);
        if (!$order) return response()->json(['message' => 'Commande non trouvée'], 404);

        return response()->json($order, 200);
    }

    // ✅ Modifier le statut de la commande
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order = Order::find($id);
        if (!$order) return response()->json(['message' => 'Commande non trouvée'], 404);

        $order->status = $request->status;
        $order->save();

        return response()->json($order, 200);
    }

    // ✅ Commandes d’un client
    public function getByUser($userId)
    {
        $orders = Order::where('user_id', $userId)->get();
        return response()->json($orders, 200);
    }
    // 🚫 Annuler une commande
    public function cancel($id)
    {
        $order = Order::find($id);
        if (!$order) return response()->json(['message' => 'Commande non trouvée'], 404);

        $order->status = 'annulée';
        $order->save();

        return response()->json(['message' => 'Commande annulée'], 200);
    }

    

    // 📦 Commandes par statut
    public function getByStatus($status)
    {
        $orders = Order::where('status', $status)->get();
        return response()->json($orders, 200);
    }

    // ⏳ Commandes en attente
    public function getPending()
    {
        $orders = Order::where('status', 'en attente')->get();
        return response()->json($orders, 200);
    }

    // 📅 Commandes par date
    public function getByDate($date)
    {
        $orders = Order::whereDate('created_at', $date)->get();
        return response()->json($orders, 200);
    }

    // 📊 Statistiques des commandes
    public function stats()
    {
        return response()->json([
            'total'      => Order::count(),
            'en_attente' => Order::where('status', 'en attente')->count(),
            'prêtes'     => Order::where('status', 'prête')->count(),
            'livrées'    => Order::where('status', 'livrée')->count(),
            'annulées'   => Order::where('status', 'annulée')->count(),
        ]);
    }
    public function search(Request $request)
{
    $name     = $request->input('name');
    $price    = $request->input('price');
    $tolerance = 5; // tolérance de ±5 par défaut (modifiable)

    $query = Order::with('user'); // on veut inclure l'utilisateur lié à la commande

    // 🔍 Si nom client fourni
    if ($name) {
        $query->whereHas('user', function ($q) use ($name) {
            $q->where('name', 'LIKE', "%$name%");
        });
    }

    // 💰 Si prix fourni
    if ($price) {
        $query->whereBetween('total_price', [
            $price - $tolerance,
            $price + $tolerance
        ]);
    }

    $orders = $query->get();

    return response()->json($orders, 200);
}

}

