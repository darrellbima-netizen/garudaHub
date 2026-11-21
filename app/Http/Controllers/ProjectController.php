<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // 1. Menampilkan semua project
    public function index()
    {
        $projects = Project::with([
            'client:id,nama,email,role',
            'bids.developer:id,nama,email,role'
        ])->latest()->get();

        return response()->json($projects);
    }

    // 2. Menampilkan detail project
    public function show($id)
    {
        $project = Project::with([
            'client:id,nama,email,role',
            'bids.developer:id,nama,email,role'
        ])->findOrFail($id);

        return response()->json($project);
    }

    // 3. Membuat project baru
    public function store(Request $request)
    {
        $user = $request->user();

        // Hanya klien yang boleh membuat project
        abort_unless($user->role === 'klien', 403,
            'Hanya klien yang dapat membuat project.');

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'required|string|max:10000',
            'kategori' => 'required|string|max:50',
            'budget_min' => 'required|integer|min:0',
            'budget_max' => 'required|integer|gte:budget_min',
            'batas_lelang' => 'required|date|after_or_equal:today',
        ]);

        // Identitas pemilik ditentukan server
        $project = $user->projects()->create([
            ...$validated,
            'status' => 'terbuka',
        ]);

        return response()->json([
            'message' => 'Project berhasil ditambahkan!',
            'data' => $project,
        ], 201);
    }

    // 4. Memperbarui project
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $project = Project::findOrFail($id);

        // Hanya pemilik atau admin yang boleh mengubah
        abort_unless(
            $user->role === 'admin' ||
            ($user->role === 'klien' &&
             $project->client_id === $user->id),
            403,
            'Anda tidak memiliki akses untuk mengubah project ini.'
        );

        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:200',
            'deskripsi' => 'sometimes|required|string|max:10000',
            'kategori' => 'sometimes|required|string|max:50',
            'budget_min' => 'sometimes|required|integer|min:0',
            'budget_max' => 'sometimes|required|integer|min:0',
            'batas_lelang' => 'sometimes|required|date|after_or_equal:today',
            'status' => 'sometimes|required|in:terbuka,ditutup',
        ]);

        $budgetMin = $validated['budget_min']
            ?? $project->budget_min;

        $budgetMax = $validated['budget_max']
            ?? $project->budget_max;

        if ($budgetMax < $budgetMin) {
            return response()->json([
                'message' => 'Budget maksimal tidak boleh lebih kecil dari budget minimal.',
            ], 422);
        }

        // client_id tidak bisa diubah melalui request
        $project->update($validated);

        return response()->json([
            'message' => 'Project berhasil diperbarui!',
            'data' => $project->fresh(),
        ]);
    }

    // 5. Menghapus project
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $project = Project::findOrFail($id);

        // Hanya pemilik atau admin yang boleh menghapus
        abort_unless(
            $user->role === 'admin' ||
            ($user->role === 'klien' &&
             $project->client_id === $user->id),
            403,
            'Anda tidak memiliki akses untuk menghapus project ini.'
        );

        $project->delete();

        return response()->json([
            'message' => 'Project berhasil dihapus!',
        ]);
    }
}
