<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Product;

class OrderItemController extends Controller
{
    // ➕ Ajouter un produit à une commande
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::find($validated['product_id']);

        $orderItem = OrderItem::create([
            'order_id'   => $validated['order_id'],
            'product_id' => $validated['product_id'],
            'quantity'   => $validated['quantity'],
        ]);

        return response()->json($orderItem, 201);
    }

    // ❌ Supprimer un produit d’une commande
    public function destroy($id)
    {
        $item = OrderItem::find($id);
        if (!$item) return response()->json(['message' => 'Produit non trouvé dans la commande'], 404);

        $item->delete();

        return response()->json(['message' => 'Produit supprimé de la commande'], 200);
    }

    // ✏️ Modifier la quantité d’un produit dans une commande
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = OrderItem::find($id);
        if (!$item) return response()->json(['message' => 'Produit non trouvé dans la commande'], 404);

        $item->quantity = $request->quantity;
        $item->save();

        return response()->json($item, 200);
    }

    // 📄 Lister tous les produits d’une commande
    public function getByOrder($orderId)
    {
        $items = OrderItem::with('product')->where('order_id', $orderId)->get();
        return response()->json($items, 200);
    }
}