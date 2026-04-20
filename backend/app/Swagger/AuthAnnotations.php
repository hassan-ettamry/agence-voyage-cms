<?php

namespace App\Swagger;

/**
 * @OA\Post(
 *     path="/auth/register",
 *     summary="Inscription d'une nouvelle agence",
 *     description="Crée une nouvelle agence et un utilisateur administrateur. L'agence est créée avec un statut 'active' et un plan 'trial' par défaut.",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"agency_name", "name", "email", "password"},
 *             @OA\Property(property="agency_name", type="string", maxLength=255, example="Voyages Évasion", description="Nom de l'agence"),
 *             @OA\Property(property="name", type="string", maxLength=255, example="Sophie Martin", description="Nom complet de l'administrateur"),
 *             @OA\Property(property="email", type="string", format="email", example="sophie.martin@voyages-evasion.com", description="Email unique"),
 *             @OA\Property(property="password", type="string", minLength=6, format="password", example="Voyage2026!", description="Mot de passe (min 6 caractères)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Inscription réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Bienvenue ! Votre agence a été créée avec succès."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="user", ref="#/components/schemas/User"),
 *                 @OA\Property(property="agency", ref="#/components/schemas/Agency"),
 *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erreur de validation",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
 *     )
 * )
 *
 * @OA\Post(
 *     path="/auth/login",
 *     summary="Connexion à l'espace agence",
 *     description="Authentifie un utilisateur et retourne un token d'accès Sanctum valable 24h.",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="sophie.martin@voyages-evasion.com"),
 *             @OA\Property(property="password", type="string", format="password", example="Voyage2026!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Connexion réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Connexion réussie. Bienvenue Sophie !"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="user", ref="#/components/schemas/User"),
 *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Identifiants invalides",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Email ou mot de passe incorrect"),
 *             @OA\Property(property="code", type="string", example="INVALID_CREDENTIALS")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erreur de validation",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/auth/me",
 *     summary="Profil de l'utilisateur connecté",
 *     description="Récupère les informations de l'utilisateur connecté avec les détails de son agence.",
 *     tags={"Authentication"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Informations utilisateur",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="user", ref="#/components/schemas/User")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     )
 * )
 *
 * @OA\Post(
 *     path="/auth/logout",
 *     summary="Déconnexion",
 *     description="Invalide tous les tokens de l'utilisateur. Déconnecte l'utilisateur de tous ses appareils.",
 *     tags={"Authentication"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Déconnexion réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Vous avez été déconnecté avec succès.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     )
 * )
 */
class AuthAnnotations
{
    //
}