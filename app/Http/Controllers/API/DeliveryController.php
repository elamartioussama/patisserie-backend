<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Order;

class DeliveryController extends Controller
{
    // 📄 Lister toutes les livraisons
    public function index()
    {
        return response()->json(Delivery::with(['order', 'livreur'])->get(), 200);
    }

    // ➕ Confirmer une livraison (ajouter)
    public function store(Request $request)
    {
        $request->validate([
            'order_id'     => 'required|exists:orders,id',
            'livreur_id'   => 'required|exists:users,id',
            'date_livraison' => 'required|date',
        ]);

        // Mettre à jour le statut de la commande à "livrée"
        $order = Order::find($request->order_id);
        $order->status = 'livrée';
        $order->save();

        $livraison = Delivery::create([
            'order_id'       => $request->order_id,
            'livreur_id'     => $request->livreur_id,
            'date_livraison' => $request->date_livraison,
        ]);

        return response()->json($livraison, 201);
    }

    // 🚚 Lister les livraisons par livreur
    public function getByLivreur($livreurId)
    {
        $livraisons = Delivery::with('order')
            ->where('livreur_id', $livreurId)
            ->get();

        return response()->json($livraisons, 200);
    }

    // 📦 Voir la livraison d’une commande
    public function getByOrder($orderId)
    {
        $livraison = Delivery::where('order_id', $orderId)->first();

        if (!$livraison) {
            return response()->json(['message' => 'Livraison non trouvée'], 404);
        }

        return response()->json($livraison, 200);
    }

    public function getByDate(Request $request)
{
    $request->validate([
        'date' => 'required|date',
    ]);

    $date = $request->date;

    $livraisons = Delivery::with(['order', 'livreur'])
        ->whereDate('date_livraison', $date)
        ->get();

    return response()->json($livraisons, 200);
}

}
