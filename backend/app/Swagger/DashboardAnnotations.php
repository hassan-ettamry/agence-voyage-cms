<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/dashboard",
 *     summary="Statistiques du tableau de bord",
 *     description="Récupère les statistiques globales pour le tableau de bord de l'agence.",
 *     tags={"Dashboard"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Statistiques du dashboard",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", ref="#/components/schemas/DashboardStats")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Non autorisé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Forbidden")
 *         )
 *     )
 * )
 */
class DashboardAnnotations
{
    //
}