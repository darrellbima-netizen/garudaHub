<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    // 1. Menampilkan semua project
    public function index()
    {
        $projects = Project::with([
            'client',
            'bids.developer'
        ])->latest()->get();

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

    // 3. Membuat project baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->where('role', 'client'),
            ],

            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string|max:50',

            'budget_min' => 'required|integer|min:0',
            'budget_max' => 'required|integer|gte:budget_min',

            'batas_lelang' => 'required|date|after_or_equal:today',

            'status' => [
                'required',
                Rule::in(['terbuka', 'ditutup']),
            ],
        ]);

        $project = Project::create($validated);

        return response()->json([
            'message' => 'Project berhasil ditambahkan!',
            'data' => $project,
        ], 201);
    }

    // 4. Memperbarui project
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'client_id' => [
                'sometimes',
                'required',
                Rule::exists('users', 'id')
                    ->where('role', 'client'),
            ],

            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'kategori' => 'sometimes|required|string|max:50',

            'budget_min' => 'sometimes|required|integer|min:0',
            'budget_max' => 'sometimes|required|integer|min:0',

            'batas_lelang' => 'sometimes|required|date',
            'status' => [
                'sometimes',
                'required',
                Rule::in(['terbuka', 'ditutup']),
            ],
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

        $project->update($validated);

        return response()->json([
            'message' => 'Project berhasil diperbarui!',
            'data' => $project->fresh(),
        ]);
    }

    // 5. Menghapus project
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        $project->delete();

        return response()->json([
            'message' => 'Project berhasil dihapus!',
        ]);
    }
}
