<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
     public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'tel' => 'nullable|string|max:20',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Message::create($request->all());

        return response()->json(['message' => 'Message reçu avec succès !'], 201);
    }
//     public function index(Request $request)
// {
//     $query = Message::query();

//     if ($request->filled('search')) {
//         $search = $request->search;
//         $query->where('name', 'like', "%$search%")
//               ->orWhere('email', 'like', "%$search%")
//               ->orWhere('tel', 'like', "%$search%");
//     }

//     $messages = $query->orderBy('created_at', 'desc')->get();

//     return response()->json($messages);
// }
public function index(Request $request)
{
    $query = Message::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhere('tel', 'like', "%$search%");
        });
    }

    if ($request->filled('date')) {
        $query->whereDate('created_at', $request->date);
    }

    $messages = $query->orderBy('created_at', 'desc')->get();

    return response()->json($messages);
}


}