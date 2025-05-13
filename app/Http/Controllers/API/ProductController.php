<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // 🔎 Afficher tous les produits
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    // ➕ Créer un produit
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|string',
            'category'    => 'nullable|string|max:100', // ✅ Ajout catégorie
        ]);
    
        $product = Product::create($validated);
    
        return response()->json($product, 201);
    }
    

    // 👁️ Afficher un produit spécifique
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) return response()->json(['message' => 'Produit non trouvé'], 404);

        return response()->json($product, 200);
    }

    // ✏️ Mettre à jour un produit
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Produit non trouvé'], 404);
    
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'stock'       => 'sometimes|integer|min:0',
            'image'       => 'nullable|string',
            'category'    => 'nullable|string|max:100', // ✅ Ajout catégorie
        ]);
    
        $product->update($validated);
    
        return response()->json($product, 200);
    }
    

    // ❌ Supprimer un produit
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Produit non trouvé'], 404);

        $product->delete();

        return response()->json(['message' => 'Produit supprimé'], 200);
    }
    public function search(Request $request)
    {
        $name = $request->input('name');
        $category = $request->input('category');

        $query = Product::query();

        if ($name) {
            $query->where('name', 'like', "%$name%");
        }

        if ($category) {
            $query->where('category', 'like', "%$category%");
        }

        $results = $query->get();

        return response()->json($results, 200);
    }
    public function filterByCategory(Request $request)
    {
        $category = $request->input('category');

        if (!$category) {
            return response()->json(['message' => 'Catégorie non spécifiée'], 400);
        }

        $products = Product::where('category', 'like', "%$category%")->get();

        return response()->json($products, 200);
    }


}
