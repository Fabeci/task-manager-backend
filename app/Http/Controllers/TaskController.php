<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {
            $tasks = Task::with('user')->where('user_id', Auth::id())->get();
            return response()->json($tasks);
        } catch (Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de la récupération des tâches.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation des données
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'status' => 'boolean',
            ]);
    
            // Création de la tâche
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'status' => $request->status,
                'user_id' => Auth::id(), // Assurez-vous que l'utilisateur est authentifié
            ]);
    
            return response()->json([
                'message' => 'La tâche a été créée avec succès.',
                $task
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ressource non trouvée.'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $task = Task::where('user_id', Auth::id())->findOrFail($id);
            return response()->json($task);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tâche non trouvée.'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de la récupération de la tâche.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $task = Task::where('user_id', Auth::id())->findOrFail($id);
    
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'status' => 'boolean',
            ]);
    
            $task->update($request->all());
    
            return response()->json($task);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tâche non trouvée.'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de la mise à jour de la tâche.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $task = Task::where('user_id', Auth::id())->findOrFail($id);
            $task->delete();
    
            return response()->json(['message' => 'Tâche supprimée avec succès']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tâche non trouvée.'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de la suppression de la tâche.', 'error' => $e->getMessage()], 500);
        }
    }
}
