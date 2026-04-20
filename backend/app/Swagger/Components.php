<?php

namespace App\Swagger;

/**
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="sanctum",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="Sanctum",
 *         description="Token d'authentification Sanctum. Format: Bearer {token}\n\n⚠️ Rate limiting: 60 requêtes par minute\n⚠️ Token expire après 24h"
 *     ),
 *     @OA\Schema(
 *         schema="StandardResponse",
 *         type="object",
 *         @OA\Property(property="success", type="boolean", example=true),
 *         @OA\Property(property="message", type="string", example="Operation completed successfully"),
 *         @OA\Property(property="data", type="object"),
 *         @OA\Property(property="meta", type="object")
 *     ),
 *     @OA\Schema(
 *         schema="ErrorResponse",
 *         type="object",
 *         @OA\Property(property="success", type="boolean", example=false),
 *         @OA\Property(property="message", type="string", example="An error occurred"),
 *         @OA\Property(property="code", type="string", example="VALIDATION_ERROR"),
 *         @OA\Property(property="errors", type="object")
 *     ),
 *     @OA\Schema(
 *         schema="ValidationError",
 *         type="object",
 *         @OA\Property(property="message", type="string", example="The given data was invalid."),
 *         @OA\Property(property="errors", type="object",
 *             @OA\AdditionalProperties(
 *                 type="array",
 *                 @OA\Items(type="string")
 *             )
 *         )
 *     ),
 *     @OA\Schema(
 *         schema="User",
 *         type="object",
 *         @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *         @OA\Property(property="name", type="string", example="Sophie Martin"),
 *         @OA\Property(property="email", type="string", format="email", example="sophie.martin@voyages-evasion.com"),
 *         @OA\Property(property="role", type="string", enum={"admin", "user"}, example="admin"),
 *         @OA\Property(property="agency_id", type="string", format="uuid", example="660e8400-e29b-41d4-a716-446655440001"),
 *         @OA\Property(property="created_at", type="string", format="date-time"),
 *         @OA\Property(property="updated_at", type="string", format="date-time")
 *     ),
 *     @OA\Schema(
 *         schema="Agency",
 *         type="object",
 *         @OA\Property(property="id", type="string", format="uuid", example="660e8400-e29b-41d4-a716-446655440001"),
 *         @OA\Property(property="name", type="string", example="Voyages Évasion"),
 *         @OA\Property(property="slug", type="string", example="voyages-evasion"),
 *         @OA\Property(property="email", type="string", format="email", example="contact@voyages-evasion.com"),
 *         @OA\Property(property="plan", type="string", enum={"trial", "basic", "premium"}, default="trial"),
 *         @OA\Property(property="status", type="string", enum={"active", "suspended", "inactive"}, default="active")
 *     ),
 *     @OA\Schema(
 *         schema="MetaSEO",
 *         type="object",
 *         @OA\Property(property="title", type="string", maxLength=255, nullable=true, example="Découvrez nos circuits accompagnés"),
 *         @OA\Property(property="description", type="string", maxLength=500, nullable=true, example="Partez à la découverte des plus belles destinations")
 *     ),
 *     @OA\Schema(
 *         schema="Page",
 *         type="object",
 *         @OA\Property(property="id", type="string", format="uuid", example="770e8400-e29b-41d4-a716-446655440002"),
 *         @OA\Property(property="title", type="string", example="Nos Circuits accompagnés"),
 *         @OA\Property(property="slug", type="string", pattern="^[a-z0-9-]+$", example="circuits-accompagnes"),
 *         @OA\Property(property="content", type="string", example="<h1>Découvrez nos circuits</h1><p>Contenu...</p>"),
 *         @OA\Property(property="status", type="string", enum={"draft", "published"}, default="draft"),
 *         @OA\Property(property="meta", ref="#/components/schemas/MetaSEO"),
 *         @OA\Property(property="agency_id", type="string", format="uuid"),
 *         @OA\Property(property="created_at", type="string", format="date-time"),
 *         @OA\Property(property="updated_at", type="string", format="date-time")
 *     ),
 *     @OA\Schema(
 *         schema="PaginatedPages",
 *         type="object",
 *         @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Page")),
 *         @OA\Property(property="links", type="object",
 *             @OA\Property(property="first", type="string"),
 *             @OA\Property(property="last", type="string"),
 *             @OA\Property(property="prev", type="string", nullable=true),
 *             @OA\Property(property="next", type="string", nullable=true)
 *         ),
 *         @OA\Property(property="meta", type="object",
 *             @OA\Property(property="current_page", type="integer"),
 *             @OA\Property(property="from", type="integer"),
 *             @OA\Property(property="last_page", type="integer"),
 *             @OA\Property(property="path", type="string"),
 *             @OA\Property(property="per_page", type="integer"),
 *             @OA\Property(property="to", type="integer"),
 *             @OA\Property(property="total", type="integer")
 *         )
 *     ),
 *     @OA\Schema(
 *         schema="DashboardStats",
 *         type="object",
 *         @OA\Property(property="pages", type="object",
 *             @OA\Property(property="total", type="integer", example=42),
 *             @OA\Property(property="draft", type="integer", example=10),
 *             @OA\Property(property="published", type="integer", example=32)
 *         )
 *     ),
 *     @OA\Schema(
 *         schema="UnauthorizedResponse",
 *         type="object",
 *         @OA\Property(property="success", type="boolean", example=false),
 *         @OA\Property(property="message", type="string", example="Unauthenticated"),
 *         @OA\Property(property="code", type="string", example="UNAUTHENTICATED")
 *     ),
 *     @OA\Schema(
 *         schema="NotFoundResponse",
 *         type="object",
 *         @OA\Property(property="success", type="boolean", example=false),
 *         @OA\Property(property="message", type="string", example="Resource not found"),
 *         @OA\Property(property="code", type="string", example="NOT_FOUND")
 *     )
 * )
 */
class Components
{
    //
}