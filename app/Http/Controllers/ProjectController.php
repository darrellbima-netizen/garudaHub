<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // 1. Menampilkan semua project
    public function index()
    {
        $projects = Project::with('client')
            ->withCount('bids')
            ->latest('created_at')
            ->get();

        return response()->json($projects);
    }

    // 2. Menampilkan detail project
    public function show($id)
    {
        $project = Project::with([
            'client',
            'bids.developer'
        ])->findOrFail($id);

        return response()->json($project);
    }

    // 3. Menambahkan project baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'judul' => 'required|string|max:200',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string|max:50',
            'budget_min' => 'required|integer|min:0',
            'budget_max' => 'required|integer|gte:budget_min',
            'batas_lelang' => 'required|date',
            'status' => 'required|in:terbuka,ditutup',
        ]);

        $project = Project::create($validated);

        return response()->json([
            'message' => 'Project berhasil ditambahkan!',
            'data' => $project
        ], 201);
    }

    // 4. Mengubah project
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:200',
            'deskripsi' => 'sometimes|required|string',
            'kategori' => 'sometimes|required|string|max:50',
            'budget_min' => 'sometimes|required|integer|min:0',
            'budget_max' => 'sometimes|required|integer|min:0',
            'batas_lelang' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:terbuka,ditutup',
        ]);

        $project->update($validated);

        return response()->json([
            'message' => 'Project berhasil diperbarui!',
            'data' => $project->fresh()
        ]);
    }

    // 5. Menghapus project
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        $project->delete();

        return response()->json([
            'message' => 'Project berhasil dihapus!'
        ]);
    }
}
