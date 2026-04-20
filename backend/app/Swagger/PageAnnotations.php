<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/pages",
 *     summary="Liste des pages",
 *     description="Récupère une liste paginée des pages avec filtres avancés, recherche et tri.",
 *     tags={"Pages"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Recherche dans le titre et le slug",
 *         required=false,
 *         @OA\Schema(type="string", example="circuit")
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Filtrer par statut de publication",
 *         required=false,
 *         @OA\Schema(type="string", enum={"draft", "published"})
 *     ),
 *     @OA\Parameter(
 *         name="from_date",
 *         in="query",
 *         description="Date de début de création (YYYY-MM-DD)",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2026-01-01")
 *     ),
 *     @OA\Parameter(
 *         name="to_date",
 *         in="query",
 *         description="Date de fin de création (YYYY-MM-DD)",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2026-12-31")
 *     ),
 *     @OA\Parameter(
 *         name="sort_by",
 *         in="query",
 *         description="Champ de tri",
 *         required=false,
 *         @OA\Schema(type="string", enum={"title", "created_at", "status"}, default="created_at")
 *     ),
 *     @OA\Parameter(
 *         name="sort_dir",
 *         in="query",
 *         description="Direction du tri",
 *         required=false,
 *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Nombre d'éléments par page (max: 100)",
 *         required=false,
 *         @OA\Schema(type="integer", minimum=1, maximum=100, default=15)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Numéro de page",
 *         required=false,
 *         @OA\Schema(type="integer", minimum=1, default=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste paginée des pages",
 *         @OA\JsonContent(ref="#/components/schemas/PaginatedPages")
 *     ),
 *     @OA\Response(response=401, description="Non authentifié", @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")),
 *     @OA\Response(response=403, description="Non autorisé")
 * )
 *
 * @OA\Post(
 *     path="/pages",
 *     summary="Créer une nouvelle page",
 *     description="Crée une nouvelle page de contenu pour l'agence. Le slug doit être unique par agence.",
 *     tags={"Pages"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "slug", "content"},
 *             @OA\Property(property="title", type="string", maxLength=255, example="Nos Meilleures Destinations 2026"),
 *             @OA\Property(property="slug", type="string", pattern="^[a-z0-9-]+$", example="meilleures-destinations-2026", description="Slug unique par agence"),
 *             @OA\Property(property="content", type="string", example="<h1>Top 10 destinations 2026</h1><p>Sélection des meilleures destinations...</p>"),
 *             @OA\Property(property="status", type="string", enum={"draft", "published"}, default="draft", example="published"),
 *             @OA\Property(property="meta", ref="#/components/schemas/MetaSEO")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Page créée avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Page créée avec succès."),
 *             @OA\Property(property="data", ref="#/components/schemas/Page")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non authentifié", @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")),
 *     @OA\Response(response=403, description="Non autorisé"),
 *     @OA\Response(response=422, description="Erreur de validation", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Get(
 *     path="/pages/{id}",
 *     summary="Détails d'une page",
 *     description="Récupère les informations complètes d'une page par son UUID.",
 *     tags={"Pages"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID de la page",
 *         @OA\Schema(type="string", format="uuid", example="770e8400-e29b-41d4-a716-446655440002")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Détails de la page",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", ref="#/components/schemas/Page")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non authentifié", @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")),
 *     @OA\Response(response=403, description="Non autorisé"),
 *     @OA\Response(response=404, description="Page non trouvée", @OA\JsonContent(ref="#/components/schemas/NotFoundResponse"))
 * )
 *
 * @OA\Put(
 *     path="/pages/{id}",
 *     summary="Modifier une page",
 *     description="Met à jour une page existante. Tous les champs sont optionnels.",
 *     tags={"Pages"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID de la page",
 *         @OA\Schema(type="string", format="uuid", example="770e8400-e29b-41d4-a716-446655440002")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", maxLength=255, example="Nos Circuits accompagnés - Été 2026"),
 *             @OA\Property(property="slug", type="string", pattern="^[a-z0-9-]+$", example="circuits-accompagnes-ete-2026"),
 *             @OA\Property(property="content", type="string", example="<h1>Nouveautés été 2026</h1><p>Découvrez nos nouveaux circuits...</p>"),
 *             @OA\Property(property="status", type="string", enum={"draft", "published"}, example="published"),
 *             @OA\Property(property="meta", ref="#/components/schemas/MetaSEO")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Page mise à jour",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Page mise à jour avec succès."),
 *             @OA\Property(property="data", ref="#/components/schemas/Page")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non authentifié", @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")),
 *     @OA\Response(response=403, description="Non autorisé"),
 *     @OA\Response(response=404, description="Page non trouvée", @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")),
 *     @OA\Response(response=422, description="Erreur de validation", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Delete(
 *     path="/pages/{id}",
 *     summary="Supprimer une page",
 *     description="Supprime définitivement une page. Action irréversible.",
 *     tags={"Pages"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID de la page",
 *         @OA\Schema(type="string", format="uuid", example="770e8400-e29b-41d4-a716-446655440002")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Page supprimée",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Page supprimée avec succès.")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non authentifié", @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")),
 *     @OA\Response(response=403, description="Non autorisé"),
 *     @OA\Response(response=404, description="Page non trouvée", @OA\JsonContent(ref="#/components/schemas/NotFoundResponse"))
 * )
 */
class PageAnnotations
{
    //
}