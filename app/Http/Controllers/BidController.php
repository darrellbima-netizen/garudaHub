<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BidController extends Controller
{
    // 1. Menampilkan semua penawaran
    public function index()
    {
        $bids = Bid::with([
            'project',
            'developer'
        ])->latest('created_at')->get();

        return response()->json($bids);
    }

    // 2. Menampilkan detail penawaran
    public function show($id)
    {
        $bid = Bid::with([
            'project',
            'developer'
        ])->findOrFail($id);

        return response()->json($bid);
    }

    // 3. Membuat penawaran baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',

            'developer_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->where('role', 'developer'),
            ],

            'harga' => 'required|integer|min:0',
            'durasi' => 'required|integer|min:1',
            'proposal' => 'required|string',
        ]);

        $bid = Bid::create($validated);

        return response()->json([
            'message' => 'Penawaran berhasil dibuat!',
            'data' => $bid
        ], 201);
    }

    // 4. Memperbarui penawaran
    public function update(Request $request, $id)
    {
        $bid = Bid::findOrFail($id);

        $validated = $request->validate([
            'harga' => 'sometimes|required|integer|min:0',
            'durasi' => 'sometimes|required|integer|min:1',
            'proposal' => 'sometimes|required|string',
        ]);

        $bid->update($validated);

        return response()->json([
            'message' => 'Penawaran berhasil diperbarui!',
            'data' => $bid->fresh()
        ]);
    }

    // 5. Menghapus penawaran
    public function destroy($id)
    {
        $bid = Bid::findOrFail($id);

        $bid->delete();

        return response()->json([
            'message' => 'Penawaran berhasil dihapus!'
        ]);
    }
}
