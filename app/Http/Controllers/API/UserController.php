<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Correcte ici

class UserController extends Controller
{
    // Afficher tous les utilisateurs
    public function index()
    {
        return response()->json(User::all(), 200);
    }
    // ➕ Créer un nouvel utilisateur
    public function store(Request $request)
    {
         $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'role' => 'required|string', // 'client', 'admin', etc.
            'tel' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'tel' => $request->tel,
            'address' => $request->address,
        ]);


        return response()->json($user, 201);
    }

    // 👁️ Afficher un utilisateur par ID
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) return response()->json(['message' => 'Utilisateur non trouvé'], 404);

        return response()->json($user, 200);
    }

    // ✏️ Mettre à jour un utilisateur
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Utilisateur non trouvé'], 404);

        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|min:6',
            'role'     => 'sometimes|in:client,admin,livreur,assembleur',
            'tel'      => 'nullable|string',
            'address'  => 'nullable|string',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user, 200);
    }

    // ❌ Supprimer un utilisateur
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Utilisateur non trouvé'], 404);

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé'], 200);
    }

    public function getLivreurs()
    {
        $livreurs = User::where('role', 'livreur')->get();
        return response()->json($livreurs, 200);
    }
    public function getAssembleurs()
    {
        $assembleurs = User::where('role', 'assembleur')->get();
        return response()->json($assembleurs, 200);
    }
    public function getClients()
    {
        $clients = User::where('role', 'client')->get();
        return response()->json($clients, 200);
    }
    public function changeRole(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Utilisateur non trouvé'], 404);

        $validated = $request->validate([
            'role' => 'required|in:client,admin,livreur,assembleur',
        ]);

        $user->role = $validated['role'];
        $user->save();

        return response()->json(['message' => 'Rôle mis à jour', 'user' => $user], 200);
    }
    // public function search(Request $request)
    // {
    //     $query = $request->input('query');

    //     $users = User::where('name', 'like', "%$query%")
    //                 ->orWhere('email', 'like', "%$query%")
    //                 ->get();

    //     return response()->json($users, 200);
    // }
    public function search(Request $request)
    {
        $name = $request->input('name');
        $role = $request->input('role');

        $query = User::query();

        if ($name) {
             $query->where(function ($q) use ($name) {
            $q->where('name', 'like', "%$name%")
            ->orWhere('email', 'like', "%$name%");
    });
            
        }

        if ($role) {
            $query->where('role', 'like', "%$role%");
        }

        $results = $query->get();

        return response()->json($results, 200);
    }

    public function getAllUsers(){
        $users = User::all();
        return response()->json($users, 200);
    }

    
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'role' => 'required|string', // 'client', 'admin', etc.
            'tel' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'tel' => $request->tel,
            'address' => $request->address,
        ]);
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'Utilisateur enregistré avec succès',
            'token' => $token,
            'user' => $user
        ]);
    }
    

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required'
    //     ]);

    //     $user = User::where('email', $request->email)->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json(['message' => 'Identifiants incorrects'], 401);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'message' => 'Connexion réussie',
    //         'token' => $token,
    //         'user' => $user
    //     ]);
    // }
//     public function login(Request $request)
// {
//     // Validation des données de la requête
//     $request->validate([
//         'email' => 'required|email',
//         'password' => 'required'
//     ]);

//     // Recherche de l'utilisateur par email
//     $user = User::where('email', $request->email)->first();

//     // Si l'utilisateur n'existe pas
//     if (!$user) {
//         return response()->json(['message' => 'Email introuvable'], 404); // Code HTTP 404 pour email non trouvé
//     }

//     // Vérification du mot de passe
//     if (!Hash::check($request->password, $user->password)) {
//         return response()->json(['message' => 'Mot de passe incorrect'], 401); // Code HTTP 401 pour mot de passe incorrect
//     }

//     // Si tout est correct, création du token et réponse avec succès
//     $token = $user->createToken('auth_token')->plainTextToken;
//     return response()->json([
//         'message' => 'Connexion réussie',
//         'token' => $token,
//         'user' => $user
//     ]);
// }
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'Email introuvable'], 404);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Mot de passe incorrect'], 401);
    }

    // Générer un token uniquement si Laravel Sanctum est bien installé
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Connexion réussie',
        'token' => $token,
        'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role, // Ici on retourne aussi le rôle
        ]
    ], 200);
}
    public function logout(Request $request)
    {
        // Supprime uniquement le token actuel (connexion actuelle)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    // Autres méthodes...
    public function updatePassword(Request $request, $id)
{
    $user = User::find($id);
    if (!$user) return response()->json(['message' => 'Utilisateur non trouvé'], 404);

    $request->validate([
        'old_password' => 'required',
        'new_password' => 'required|confirmed|min:6',
    ]);

    if (!Hash::check($request->old_password, $user->password)) {
        return response()->json(['message' => 'Ancien mot de passe incorrect'], 403);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json(['message' => 'Mot de passe mis à jour avec succès']);
}

}