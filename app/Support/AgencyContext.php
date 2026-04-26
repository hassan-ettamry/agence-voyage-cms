<?php

namespace App\Support;

class AgencyContext
{
    /**
     * Identifiant de l'agence courante
     * Stocké de manière statique pour être accessible globalement
     */
    protected static ?string $agencyId = null;

    /**
     * Définir le contexte d'agence courant
     *
     * @param string|null $agencyId
     */
    public static function set(?string $agencyId): void
    {
        static::$agencyId = $agencyId;
    }

    /**
     * Récupérer l'identifiant de l'agence courante
     *
     * @return string|null
     */
    public static function get(): ?string
    {
        return static::$agencyId;
    }

    /**
     * Vérifier si un contexte d'agence est défini
     *
     * @return bool
     */
    public static function has(): bool
    {
        return !is_null(static::$agencyId);
    }

    /**
     * Réinitialiser le contexte d'agence
     *
     * Utile notamment en fin de requête ou lors de changements de contexte
     */
    public static function clear(): void
    {
        static::$agencyId = null;
    }
}