<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Order;

class ComplaintController extends Controller
{
    // 📄 Lister toutes les réclamations
    public function index()
    {
        return response()->json(Complaint::with('order')->get(), 200);
    }

    // ➕ Soumettre une réclamation
    public function store(Request $request)
    {
        $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'client_id'  => 'required|exists:users,id',
            'message'    => 'required|string|max:500',
        ]);

        $reclamation = Complaint::create([
            'order_id'   => $request->order_id,
            'client_id'  => $request->client_id,
            'message'    => $request->message,
            'status'     => 'en attente',
        ]);

        return response()->json($reclamation, 201);
    }

    // 👁️ Voir une réclamation spécifique
    public function show($id)
    {
        $reclamation = Complaint::with('order')->find($id);
        if (!$reclamation) {
            return response()->json(['message' => 'Réclamation non trouvée'], 404);
        }

        return response()->json($reclamation, 200);
    }

    // ✏️ Modifier le statut d’une réclamation
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:en attente,traitée,fermée',
        ]);

        $reclamation = Complaint::find($id);
        if (!$reclamation) {
            return response()->json(['message' => 'Réclamation non trouvée'], 404);
        }

        $reclamation->status = $request->status;
        $reclamation->save();

        return response()->json($reclamation, 200);
    }

    // ❌ Supprimer une réclamation
    public function destroy($id)
    {
        $reclamation = Complaint::find($id);
        if (!$reclamation) {
            return response()->json(['message' => 'Réclamation non trouvée'], 404);
        }

        $reclamation->delete();

        return response()->json(['message' => 'Réclamation supprimée'], 200);
    }

    // 📄 Lister les réclamations d’un client
    public function getByClient($clientId)
    {
        $reclamations = Complaint::where('client_id', $clientId)->get();
        return response()->json($reclamations, 200);
    }
    public function getByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;

        $reclamations = Complaint::with(['order', 'client'])
            ->whereDate('created_at', $date)
            ->get();

        return response()->json($reclamations, 200);
    }
    public function getByStatus($status)
    {
        $allowedStatuses = ['en attente', 'traitée', 'fermée'];

        if (!in_array($status, $allowedStatuses)) {
            return response()->json(['message' => 'Statut invalide'], 400);
        }

        $reclamations = Complaint::with(['order', 'client'])
            ->where('status', $status)
            ->get();

        return response()->json($reclamations, 200);
    }


}
