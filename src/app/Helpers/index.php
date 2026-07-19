<?php

if (!function_exists('__t')) {
    /**
     * Simplifica la traducción de laravel-modules.
     *
     * @param string $key Clave de la traducción (ej: 'users_management')
     * @param string $module Nombre del módulo (ej: 'administration')
     * @param string $file Nombre del archivo de idioma (por defecto 'layout')
     * @return string
     */
    function __t(string $key, ?string $module = null, string $file = 'layout'): string
    {
        if (is_null($module)) {
            return __("{$file}.{$key}");
        }

        // Esto construye dinámicamente: administration::layout.users_management
        return __("{$module}::{$file}.{$key}");
    }
}
