<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BidController extends Controller
{
    // 1. Menampilkan penawaran sesuai hak akses
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Bid::with([
            'project',
            'developer:id,nama,email,role'
        ]);

        if ($user->role === 'developer') {
            $query->where('developer_id', $user->id);

        } elseif ($user->role === 'klien') {
            $query->whereHas('project', function ($q) use ($user) {
                $q->where('client_id', $user->id);
            });

        } elseif ($user->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        return response()->json($query->latest()->get());
    }

    // 2. Menampilkan detail penawaran
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $bid = Bid::with([
            'project',
            'developer:id,nama,email,role'
        ])->findOrFail($id);

        $allowed =
            $user->role === 'admin' ||
            (
                $user->role === 'developer' &&
                $bid->developer_id === $user->id
            ) ||
            (
                $user->role === 'klien' &&
                $bid->project->client_id === $user->id
            );

        abort_unless($allowed, 403, 'Akses penawaran ditolak.');

        return response()->json($bid);
    }

    // 3. Membuat penawaran baru
    public function store(Request $request)
    {
        $user = $request->user();

        // Hanya developer yang boleh mengirim bid
        abort_unless($user->role === 'developer', 403,
            'Hanya developer yang dapat mengirim penawaran.');

        $validated = $request->validate([
            'project_id' => [
                'required',
                Rule::exists('projects', 'id')
                    ->where('status', 'terbuka'),
            ],

            'harga' => 'required|integer|min:1',
            'durasi' => 'required|integer|min:1',
            'proposal' => 'required|string|max:10000',
        ]);

        $project = Project::findOrFail($validated['project_id']);

        // Developer tidak boleh menawar project sendiri
        abort_if(
            $project->client_id === $user->id,
            403,
            'Anda tidak dapat menawar project milik sendiri.'
        );

        // Cegah developer mengirim penawaran dua kali
        $alreadyBid = Bid::where('project_id', $project->id)
            ->where('developer_id', $user->id)
            ->exists();

        if ($alreadyBid) {
            return response()->json([
                'message' => 'Anda sudah mengirim penawaran untuk project ini.',
            ], 422);
        }

        // Identitas developer ditentukan server
        $bid = $user->bids()->create([
            'project_id' => $project->id,
            'harga' => $validated['harga'],
            'durasi' => $validated['durasi'],
            'proposal' => $validated['proposal'],
        ]);

        $bid->load(['project', 'developer']);

        return response()->json([
            'message' => 'Penawaran berhasil dibuat!',
            'data' => $bid,
        ], 201);
    }

    // 4. Memperbarui penawaran
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $bid = Bid::findOrFail($id);

        $allowed =
            $user->role === 'admin' ||
            (
                $user->role === 'developer' &&
                $bid->developer_id === $user->id
            );

        abort_unless($allowed, 403,
            'Anda tidak memiliki akses untuk mengubah penawaran ini.');

        abort_if($bid->is_winner, 403,
            'Penawaran pemenang tidak dapat diubah.');

        $validated = $request->validate([
            'harga' => 'sometimes|required|integer|min:1',
            'durasi' => 'sometimes|required|integer|min:1',
            'proposal' => 'sometimes|required|string|max:10000',
        ]);

        $bid->update($validated);

        return response()->json([
            'message' => 'Penawaran berhasil diperbarui!',
            'data' => $bid->fresh()->load([
                'project',
                'developer'
            ]),
        ]);
    }

    // 5. Menghapus penawaran
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $bid = Bid::findOrFail($id);

        $allowed =
            $user->role === 'admin' ||
            (
                $user->role === 'developer' &&
                $bid->developer_id === $user->id
            );

        abort_unless($allowed, 403,
            'Anda tidak memiliki akses untuk menghapus penawaran ini.');

        abort_if($bid->is_winner, 403,
            'Penawaran pemenang tidak dapat dihapus.');

        $bid->delete();

        return response()->json([
            'message' => 'Penawaran berhasil dihapus!',
        ]);
    }
}
