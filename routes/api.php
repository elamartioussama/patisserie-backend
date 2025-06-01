<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OrderItemController;
use App\Http\Controllers\API\DeliveryController;
use App\Http\Controllers\API\ComplaintController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\MessageController;

Route::middleware('api')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::put('/users/{id}/password', [UserController::class, 'updatePassword']);


    
// 📦 Toutes les routes UserController

// CRUD basique
Route::get('/users', [UserController::class, 'index']);          // Lister tous les utilisateurs
Route::post('users', [UserController::class, 'store']);          // Créer un utilisateur
Route::get('users/{id}', [UserController::class, 'show']);       // Afficher un utilisateur par ID
Route::put('users/{id}', [UserController::class, 'update']);     // Mettre à jour un utilisateur
Route::delete('users/{id}', [UserController::class, 'destroy']); // Supprimer un utilisateur -->

// 🔍 Recherche
Route::get('users-search', [UserController::class, 'search']); // Rechercher utilisateur par nom ou email (?query=...)
Route::get('/roles', function () {
    return \App\Models\User::distinct()->pluck('role');
});

// 🎯 Rôles spécifiques
Route::get('users-livreurs', [UserController::class, 'getLivreurs']);       // Liste des livreurs
Route::get('users-assembleurs', [UserController::class, 'getAssembleurs']); // Liste des assembleurs

// 🔁 Changement de rôle
Route::put('users/{id}/role', [UserController::class, 'changeRole']); // Modifier le rôle d’un utilisateur
// http://localhost:8000/api/users-search?query=ahmed

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);



//
// 📦 PRODUITS : Routes individuelles comme UserController
//

// ➕ Créer un produit
Route::post('products', [ProductController::class, 'store']);

// 📄 Lister tous les produits
Route::get('products', [ProductController::class, 'index']);

// 👁️ Afficher un produit spécifique
Route::get('products/{id}', [ProductController::class, 'show']);

// ✏️ Modifier un produit
Route::put('products/{id}', [ProductController::class, 'update']);

// ❌ Supprimer un produit
Route::delete('products/{id}', [ProductController::class, 'destroy']);

// 🔍 Recherche par nom et catégorie
Route::get('products-search', [ProductController::class, 'search']);
// http://localhost:8000/api/products-search?name=tarte&category=fruit


// 🎯 Filtrer par catégorie
Route::get('products/filter/category', [ProductController::class, 'filterByCategory']);
// http://localhost:8000/api/products/filter/category?category=fruit

Route::get('/categories', function () {
    return \App\Models\Product::distinct()->pluck('category');
});

Route::post('/cart/add', [OrderController::class, 'addToCart']);
// routes/api.php


Route::get('/cartt/{user_id}', [OrderController::class, 'getCart']);

Route::post('/check-cart', [CartController::class, 'checkCart']);
Route::post('/update-cart', [CartController::class, 'updateCart']);
Route::post('/remove-from-cart', [CartController::class, 'removeFromCart']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);



Route::post('/messages', [MessageController::class, 'store']);
Route::get('/admin/messages', [MessageController::class, 'index']);



    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy1']);
    
    Route::put('orders/{order}/items', [OrderController::class, 'updateItems']);

Route::get('orders/client/{userId}', [OrderController::class, 'clientOrders']);

Route::get('/orders/{order}/pdf', [OrderController::class, 'generatePdf']);

Route::get('/statistics', [ComplaintController::class, 'general']);
Route::get('/dashboard-stats', [ComplaintController::class, 'stats']);



});


Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);


?>