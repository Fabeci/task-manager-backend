<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|min:3|max:100',
                'email' => 'required|email|max:50|unique:users',
                'password' => 'required|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Inscription réussie',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validation des entrées
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            // Recherche de l'utilisateur par email
            $user = User::where('email', $request->email)->first();
    
            // Vérification des identifiants
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Identifiants incorrects'
                ], 401);
            }
    
            // Création du token
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $user,
                'token' => $token
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Gestion des erreurs de validation
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            // Gestion des autres exceptions
            return response()->json([
                'message' => 'Une erreur est survenue lors de la connexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        
        dd($request->user());
        try {
            if (!$request->user()) {
                return response()->json(['message' => 'Utilisateur non authentifié.'], 401);
            }
            // Vérifie si l'utilisateur est authentifié
            if ($request->user() && $request->user()->currentAccessToken()) {
                // Supprime le token d'accès actuel de l'utilisateur
                $request->user()->currentAccessToken()->delete();
                return response()->json(['message' => 'Déconnexion réussie'], 200);
            } else {
                // Si aucun utilisateur authentifié ou pas de token, renvoie une erreur
                return response()->json(['message' => 'Utilisateur non authentifié ou token non trouvé'], 401);
            }
        } catch (\Exception $e) {
            // Capture les exceptions inattendues et retourne un message d'erreur
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
