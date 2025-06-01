<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = $request->input('user_id');
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // Vérifier si produit existe et stock suffisant
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }
        if ($product->stock < $quantity) {
            return response()->json(['message' => "Stock insuffisant, disponible : $product->stock"], 400);
        }

        // Chercher une commande "en_attente" pour l'utilisateur
        $order = Order::where('user_id', $userId)
                      ->where('status', 'en_attente')
                      ->first();

        // Si pas de commande en attente, on en crée une
        if (!$order) {
            $order = Order::create([
                'user_id' => $userId,
                'status' => 'en_attente',
                'total_price' => 0,
            ]);
        }

        // Chercher si produit déjà dans la commande
        $orderItem = OrderItem::where('order_id', $order->id)
                              ->where('product_id', $productId)
                              ->first();

        if ($orderItem) {
            // Mise à jour de la quantité (ajout)
            $newQuantity = $orderItem->quantity + $quantity;
            if ($newQuantity > $product->stock) {
                return response()->json(['message' => "Stock insuffisant, disponible : $product->stock"], 400);
            }
            $orderItem->quantity = $newQuantity;
            $orderItem->save();
        } else {
            // Création du nouvel item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        // Recalculer le total de la commande
        $total = 0;
        foreach ($order->items as $item) {
            $total += $item->quantity * $item->product->price;
        }
        $order->total_price = $total;
        $order->save();

        return response()->json(['message' => 'Produit ajouté au panier', 'order' => $order]);
    }
    public function getCart($user_id)
{
    $order = Order::where('user_id', $user_id)
        ->where('status', 'en_attente') // tu as dit que tu utilises 'en_attente'
        ->with('products') // ou 'orderItems.products' selon ta relation
        ->first();

    if (!$order) {
        return response()->json([
            'message' => 'Panier vide',
            'order_items' => [],
            'total_price' => 0
        ], 200);
    }

    return response()->json([
        'order' => $order,
        'order_items' => $order->products,
        'total_price' => $order->total_price
    ]);
}
public function destroy($id)
{
    $order = Order::findOrFail($id);
    $order->delete();
    return response()->json(['message' => 'Commande supprimée.']);
}

// public function index(Request $request)
//     {
//         $query = Order::with(['user', 'items.product']);

//         if ($search = $request->input('search')) {
//             $query->whereHas('user', function ($q) use ($search) {
//                 $q->where('name', 'like', "%$search%")
//                   ->orWhere('email', 'like', "%$search%")
//                   ->orWhere('tel', 'like', "%$search%");
//             });
//         }

//         if ($date = $request->input('date')) {
//             $query->whereDate('created_at', $date);
//         }

//         $orders = $query->orderBy('created_at', 'desc')->get();

//         return response()->json($orders);
//     }
public function index(Request $request)
{
    $query = Order::with(['user', 'items.product']);

    if ($search = $request->input('search')) {
        $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhere('tel', 'like', "%$search%");
        });
    }

    if ($date = $request->input('date')) {
        $query->whereDate('created_at', $date);
    }

    if ($status = $request->input('status')) {
        // Filtrer par statut uniquement si status n'est pas vide
        $query->where('status', $status);
    }

    $orders = $query->orderBy('created_at', 'desc')->get();

    return response()->json($orders);
}

    // Détail d'une commande
    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return response()->json($order);
    }

    // Mise à jour du statut
    // public function updateStatus(Request $request, $id)
    // {
    //     $request->validate(['status' => 'required|string']);
    //     $order = Order::findOrFail($id);
    //     $order->status = $request->status;
    //     $order->save();

    //     return response()->json(['message' => 'Statut mis à jour']);
    // }
    public function updateStatus(Request $request, $id)
{
    $order = Order::findOrFail($id);
    $newStatus = $request->input('status');
    $currentStatus = $order->status;

    $validTransitions = [
        'en_attente' => ['en_cours'],
        'en_cours' => ['prête'],
        'prête' => ['livrée'],
        'livrée' => ['en_attente'],
    ];

    if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
        return response()->json(['error' => "Transition non autorisée de $currentStatus à $newStatus."], 400);
    }

    $order->status = $newStatus;
    $order->save();

    return response()->json(['message' => 'Statut mis à jour']);
}


    // Suppression d'une commande
    public function destroy1($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Commande supprimée']);
    }
 public function clientOrders($userId)
    {
        $orders = Order::with('items.product') // on charge aussi le produit lié aux orderItems
                    ->where('user_id', $userId)
                    ->get();

    return response()->json($orders);
    }
//     public function updateItems(Request $request, Order $order)
// {
//     $request->validate([
//         'items' => 'required|array',
//         'items.*.product_id' => 'required|exists:products,id',
//         'items.*.quantity' => 'required|integer|min:1',
//     ]);

//     // Récupérer les données
//     $newItems = collect($request->items);

//     // Commencer une transaction pour être safe
//     \DB::transaction(function () use ($order, $newItems) {
//         // Supprimer tous les order_items actuels
//         $order->items()->delete();

//         // Recréer les order_items
//         foreach ($newItems as $item) {
//             $order->items()->create([
//                 'product_id' => $item['product_id'],
//                 'quantity' => $item['quantity'],
//             ]);
//         }
//     });

//     return response()->json([
//         'message' => 'Les articles de la commande ont été mis à jour.',
//         'order' => $order->load('items.product'),
//     ]);
// }
public function updateItems(Request $request, Order $order)
{
    $request->validate([
        'items' => 'required|array',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    $newItems = collect($request->items);

    \DB::transaction(function () use ($order, $newItems) {
        // Supprimer les anciens items
        $order->items()->delete();

        $total = 0;

        foreach ($newItems as $item) {
            $product = Product::find($item['product_id']);
            $price = $product->price; // ou le champ qui contient le prix
            $quantity = $item['quantity'];
            
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
            ]);

            $total += $price * $quantity;
        }

        // Mettre à jour le montant total de la commande
        $order->total_price = $total;
        $order->save();
    });

    return response()->json([
        'message' => 'Les articles de la commande ont été mis à jour.',
        'order' => $order->load('items.product'),
    ]);
}
public function generatePdf(Order $order)
{
    $order->load('items.product');

    $pdf = Pdf::loadView('pdf.ticket', compact('order'));

    return $pdf->download('ticket_commande_'.$order->id.'.pdf');
}

};
