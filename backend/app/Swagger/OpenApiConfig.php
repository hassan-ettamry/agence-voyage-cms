<?php

namespace App\Swagger;

/**
 * @OA\OpenApi(
 *     openapi="3.0.0",
 *     @OA\Info(
 *         title="Agence Voyage CMS API",
 *         version="2.0.0",
 *         description="API de gestion de contenu pour agences de voyage",
 *         termsOfService="https://api.agence-voyage.com/terms",
 *         @OA\Contact(
 *             name="Support API",
 *             email="support@agence-voyage.com",
 *             url="https://agence-voyage.com/support"
 *         ),
 *         @OA\License(
 *             name="MIT",
 *             url="https://opensource.org/licenses/MIT"
 *         )
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8000/api",
 *         description="Serveur de développement"
 *     ),
 *     @OA\Server(
 *         url="https://api.agence-voyage.com/api/v1",
 *         description="Serveur de production"
 *     ),
 *     @OA\ExternalDocumentation(
 *         description="Documentation complète",
 *         url="https://docs.agence-voyage.com"
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints d'authentification - Inscription, connexion, profil, déconnexion"
 * )
 * @OA\Tag(
 *     name="Pages",
 *     description="Gestion des pages - CRUD complet avec filtres, recherche et pagination"
 * )
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Statistiques du tableau de bord"
 * )
 */
class OpenApiConfig
{
    //
}