<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OrderItemController;
use App\Http\Controllers\API\DeliveryController;
use App\Http\Controllers\API\ComplaintController;


Route::middleware('api')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    
// 📦 Toutes les routes UserController

// CRUD basique
Route::get('/users', [UserController::class, 'index']);          // Lister tous les utilisateurs
Route::post('users', [UserController::class, 'store']);          // Créer un utilisateur
Route::get('users/{id}', [UserController::class, 'show']);       // Afficher un utilisateur par ID
Route::put('users/{id}', [UserController::class, 'update']);     // Mettre à jour un utilisateur
Route::delete('users/{id}', [UserController::class, 'destroy']); // Supprimer un utilisateur -->

// 🔍 Recherche
Route::get('users-search', [UserController::class, 'search']); // Rechercher utilisateur par nom ou email (?query=...)


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





//
// 📦 COMMANDES
//

// ➕ Créer une commande
Route::post('orders', [OrderController::class, 'store']);

// 📄 Lister toutes les commandes
Route::get('orders', [OrderController::class, 'index']);

// 👁️ Voir une commande spécifique
Route::get('orders/{id}', [OrderController::class, 'show']);

// ✏️ Modifier le statut d’une commande
Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);

// 🚫 Annuler une commande
Route::put('orders/{id}/cancel', [OrderController::class, 'cancel']);

// 👤 Commandes d’un client
Route::get('orders/user/{userId}', [OrderController::class, 'getByUser']);

// 📦 Commandes par statut
Route::get('orders/status/{status}', [OrderController::class, 'getByStatus']);

// ⏳ Commandes en attente
Route::get('orders/pending', [OrderController::class, 'getPending']);

// 📅 Commandes par date
Route::get('orders/date/{date}', [OrderController::class, 'getByDate']);

// 📊 Statistiques des commandes
Route::get('orders/stats', [OrderController::class, 'stats']);

// 🔍 Rechercher commande par nom client ou prix (approximatif)
Route::get('orders-search', [OrderController::class, 'search']);
// GET /api/orders-search?name=samia
// GET /api/orders-search?price=50
// GET /api/orders-search?name=samia&price=50



//
// 🧾 ORDER ITEMS (produits dans une commande)
//

// ➕ Ajouter un produit à une commande
Route::post('order-items', [OrderItemController::class, 'store']);

// ❌ Supprimer un produit d’une commande
Route::delete('order-items/{id}', [OrderItemController::class, 'destroy']);

// ✏️ Modifier la quantité d’un produit
Route::put('order-items/{id}', [OrderItemController::class, 'update']);

// 📄 Lister tous les produits d’une commande
Route::get('order-items/order/{orderId}', [OrderItemController::class, 'getByOrder']);



//
// 🚚 LIVRAISONS
//

// 📄 Lister toutes les livraisons
Route::get('livraisons', [DeliveryController::class, 'index']);

// ➕ Confirmer une livraison
Route::post('livraisons', [DeliveryController::class, 'store']);

// 🚚 Lister les livraisons d’un livreur
Route::get('livraisons/livreur/{livreurId}', [DeliveryController::class, 'getByLivreur']);

// 📦 Voir la livraison d’une commande
Route::get('livraisons/order/{orderId}', [DeliveryController::class, 'getByOrder']);

// 📅 Lister les livraisons par date
Route::get('livraisons/by-date', [DeliveryController::class, 'getByDate']);



//
// 📝 RÉCLAMATIONS
//

// 📄 Lister toutes les réclamations
Route::get('reclamations', [ComplaintController::class, 'index']);

// ➕ Créer une réclamation
Route::post('reclamations', [ComplaintController::class, 'store']);

// 👁️ Voir une réclamation par ID
Route::get('reclamations/{id}', [ComplaintController::class, 'show']);

// ✏️ Modifier le statut d'une réclamation
Route::put('reclamations/{id}', [ComplaintController::class, 'update']);

// ❌ Supprimer une réclamation
Route::delete('reclamations/{id}', [ComplaintController::class, 'destroy']);

// 📄 Lister les réclamations d’un client
Route::get('reclamations/client/{clientId}', [ComplaintController::class, 'getByClient']);

// 📅 Lister les réclamations par date
Route::get('reclamations/by-date', [ComplaintController::class, 'getByDate']);
// /api/reclamations/by-date?date=2025-04-22

// 🏷️ Lister les réclamations par statut
Route::get('reclamations/status/{status}', [ComplaintController::class, 'getByStatus']);
// /api/reclamations/status/en attente

});
// Route::post('/login', [UserController::class, 'login']);

// Route::middleware(['auth:sanctum'])->group(function () {

//     // 👑 Admin seulement
//     Route::middleware(['role:admin'])->group(function () {
//         // Route::resource('users', UserController::class);
//         Route::get('/users', [UserController::class, 'index']);  
//         Route::put('users/{id}/role', [UserController::class, 'changeRole']);
//         Route::post('products', [ProductController::class, 'store']);
//         Route::put('products/{id}', [ProductController::class, 'update']);
//         Route::delete('products/{id}', [ProductController::class, 'destroy']);
//         Route::delete('reclamations/{id}', [ComplaintController::class, 'destroy']);
//     });

//     // 🧑 Client
//     Route::middleware(['role:client'])->group(function () {
//         Route::post('orders', [OrderController::class, 'store']);
//         Route::get('orders/user/{userId}', [OrderController::class, 'getByUser']);
//         Route::put('orders/{id}/cancel', [OrderController::class, 'cancel']);
//         Route::post('reclamations', [ComplaintController::class, 'store']);
//         Route::get('reclamations/client/{clientId}', [ComplaintController::class, 'getByClient']);
//     });

//     // 🛠️ Assembleur
//     Route::middleware(['role:assembleur'])->group(function () {
//         Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);
//         Route::get('orders/status/{status}', [OrderController::class, 'getByStatus']);
//         Route::get('orders/pending', [OrderController::class, 'getPending']);
//     });

//     // 🚚 Livreur
//     Route::middleware(['role:livreur'])->group(function () {
//         Route::post('livraisons', [DeliveryController::class, 'store']);
//         Route::get('livraisons/livreur/{livreurId}', [DeliveryController::class, 'getByLivreur']);
//     });

//     // 🔓 Tous les rôles authentifiés
//     Route::get('products', [ProductController::class, 'index']);
//     Route::get('products/{id}', [ProductController::class, 'show']);
//     Route::get('products-search', [ProductController::class, 'search']);
//     Route::get('products/filter/category', [ProductController::class, 'filterByCategory']);
// });

Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);


?>