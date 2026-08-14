<?php

if (!function_exists('__t')) {
    /**
     * Simplifica la traducción de laravel-modules.
     *
     * @param string $key Clave de la traducción (ej: 'users_management')
     * @param string|null $module Nombre del módulo (ej: 'administration')
     * @param string|array $file Nombre del archivo de idioma (por defecto 'layout') o array de reemplazos
     * @param array $replace Parámetros de reemplazo para la traducción
     * @return string
     */
    function __t(string $key, ?string $module = null, string|array $file = 'layout', array $replace = []): string
    {
        if (is_array($file)) {
            $replace = $file;
            $file = 'layout';
        }

        if (is_null($module)) {
            return __("{$file}.{$key}", $replace);
        }

        // Esto construye dinámicamente: administration::layout.users_management
        return __("{$module}::{$file}.{$key}", $replace);
    }
}
