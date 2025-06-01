<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class CartController extends Controller
{
    private function recalculateOrderTotal(Order $order)
{
    $total = 0;
    foreach ($order->products as $product) {
        $total += $product->price * $product->pivot->quantity;
    }
    $order->total_price = $total;
    $order->save();
}

    // Vérifier s'il y a une commande en attente
    public function checkCart(Request $request)
    {
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json(['error' => 'user_id manquant'], 400);
        }

        $order = Order::where('user_id', $userId)->where('status', 'en_attente')->first();

        if (!$order) {
            return response()->json([
                'order_id' => null,
                'products' => []
            ]);
        }

        $products = $order->products()->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
                'stock' => $product->stock,
                'image'=> $product->image,            ];
        });

        return response()->json([
            'order_id' => $order->id,
            'products' => $products
        ]);
    }

    // Met à jour la quantité d'un produit dans la commande
    public function updateCart(Request $request)
    {
        $userId = $request->input('user_id');
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        if (!$userId || !$productId || !$quantity) {
            return response()->json(['error' => 'Champs manquants'], 400);
        }

        $order = Order::where('user_id', $userId)->where('status', 'en_attente')->first();

        if (!$order) {
            return response()->json(['error' => 'Commande introuvable'], 404);
        }

        $product = $order->products()->where('product_id', $productId)->first();

        if (!$product) {
            return response()->json(['error' => 'Produit non présent dans la commande'], 404);
        }

        if ($quantity > $product->stock) {
            return response()->json(['error' => "Stock insuffisant, disponible: {$product->stock}"], 400);
        }

        $order->products()->updateExistingPivot($productId, ['quantity' => $quantity]);
        $this->recalculateOrderTotal($order);

        // Renvoie les produits mis à jour
        $products = $order->products()->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
                'stock' => $product->stock,
                'image'=> $product->image,  
            ];
        });

        return response()->json([
            'message' => 'Quantité mise à jour',
            'products' => $products,
            'total' => $order->total,
        ]);
    }
   

    // Supprime un produit de la commande
    public function removeFromCart(Request $request)
    {
        $userId = $request->input('user_id');
        $productId = $request->input('product_id');

        if (!$userId || !$productId) {
            return response()->json(['error' => 'Champs manquants'], 400);
        }

        $order = Order::where('user_id', $userId)->where('status', 'en_attente')->first();

        if (!$order) {
            return response()->json(['error' => 'Commande introuvable'], 404);
        }

        $order->products()->detach($productId);
        $this->recalculateOrderTotal($order);

        // Renvoie les produits mis à jour
        $products = $order->products()->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
                'stock' => $product->stock,
                'image'=> $product->image,  
            ];
        });

        return response()->json([
            'message' => 'Produit supprimé du panier',
            'products' => $products
        ]);
    }
}

