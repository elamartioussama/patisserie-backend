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
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         // 'name'        => 'required|string|max:255',
    //         // 'description' => 'nullable|string',
    //         // 'price'       => 'required|numeric|min:0',
    //         // 'stock'       => 'required|integer|min:0',
    //         // 'image'       => 'nullable|string',
    //         // 'category'    => 'nullable|string|max:100', // ✅ Ajout catégorie
    //     ]);
    
    //     $product = Product::create($validated);
    
    //     return response()->json($product, 201);
    // }
//     public function store(Request $request)
// {
//     $product = new Product();
    // $product->name = $request->input('name'); // <- Ce champ est null !
    // $product->price = $request->input('price');
    // $product->description = $request->input('description');
    
    // $product->stock = $request->input("stock");
    // $product->category = $request->input('category');
    // $product->image = $request->input('image');
//     $product->save();

//     return response()->json(['message' => 'Produit ajouté avec succès'], 201);
// }
public function store(Request $request)
{
    // Validation, y compris le fichier image
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        // autres règles...
    ]);

    $product = new Product();
    $product->name = $request->input('name'); // <- Ce champ est null !
    $product->price = $request->input('price');
    $product->description = $request->input('description');
    
    $product->stock = $request->input("stock");
    $product->category = $request->input('category');
    $product->image = $request->input('image');
    // autres champs...

    // Gestion de l'image uploadée
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        // Générer un nom unique
        $filename = time().'_'.$file->getClientOriginalName();
        // Déplacer le fichier dans public/storage/images (par exemple)
        $file->move(public_path('storage/images'), $filename);
        // Enregistrer le chemin ou le nom dans la base
        $product->image = 'storage/images/' . $filename;
    }

    $product->save();

    return response()->json(['message' => 'Produit créé avec succès', 'product' => $product], 201);
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
            'category'    => 'nullable|string|max:100', // ✅ Ajout catégorie
            
        ]);
       if ($request->hasFile('image')) {
    $file = $request->file('image');
    $filename = time().'_'.$file->getClientOriginalName();
    $file->move(public_path('storage/images'), $filename);
    $product->image = 'storage/images/' . $filename;
}
    
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
