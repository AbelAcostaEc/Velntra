<?php

return [
    // --- USUARIOS ---
    // Page
    'users'                             => 'Usuarios',
    'users_management'                  => 'Gestión de usuarios',
    'users_management_description'      => 'Administra los usuarios del sistema y sus roles de acceso.',

    // Stat cards
    'total_users'                       => 'Total Usuarios',
    'registered'                        => 'Registrados',
    'admins'                            => 'Administradores',
    'full_access'                       => 'Acceso Total',
    'sellers'                           => 'Vendedores',
    'operational_access'                => 'Acceso Operativo',

    // Table
    'search_placeholder'                => 'Buscar por nombre o correo...',
    'col_name'                          => 'Nombre',
    'col_email'                         => 'Correo Electrónico',
    'col_role'                          => 'Rol',
    'col_actions'                       => 'Acciones',

    // Empty state
    'no_users_title'                    => 'No hay usuarios registrados',
    'no_users_description'              => 'No se encontraron usuarios que coincidan con la búsqueda.',
    'no_role'                           => 'Sin rol',

    // Row actions
    'edit'                              => 'Editar',
    'delete'                            => 'Eliminar',
    'create_user'                       => 'Crear Usuario',

    // Form modal
    'edit_user'                         => 'Editar Usuario',
    'form_description'                  => 'Completa la información del usuario a continuación.',
    'field_name'                        => 'Nombre Completo',
    'field_name_placeholder'            => 'Ej. Juan Pérez',
    'field_email'                       => 'Correo Electrónico',
    'field_password'                    => 'Contraseña',
    'field_password_edit_placeholder'   => 'Dejar en blanco para mantener la actual',
    'field_password_create_placeholder' => 'Mínimo 8 caracteres',
    'field_role'                        => 'Rol de Acceso',
    'cancel'                            => 'Cancelar',
    'save'                              => 'Guardar',
    'saving'                            => 'Guardando...',

    // Delete modal
    'delete_user_title'                 => 'Eliminar Usuario',
    'delete_user_description'           => '¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.',

    // Toast notifications
    'user_created'                      => 'Usuario creado exitosamente.',
    'user_updated'                      => 'Usuario actualizado exitosamente.',
    'user_deleted'                      => 'Usuario eliminado exitosamente.',

    // --- ROLES ---
    // Page
    'roles'                             => 'Roles',
    'roles_management'                  => 'Gestión de roles',
    'roles_management_description'      => 'Administra los roles del sistema y sus permisos asignados.',

    // Stat cards
    'total_roles'                       => 'Total Roles',
    'total_permissions'                 => 'Total Permisos',
    'assigned_users'                    => 'Usuarios con Rol',
    'configured_roles'                  => 'Roles Configurados',
    'available_permissions'             => 'Permisos Disponibles',
    'active_assignments'                => 'Asignaciones Activas',

    // Table
    'search_roles_placeholder'          => 'Buscar rol por nombre...',
    'col_permissions'                   => 'Permisos Asignados',
    'col_users'                         => 'Usuarios Asignados',
    'permissions_count'                 => 'Permisos',
    'users_count'                       => 'Usuarios',
    'all_permissions'                   => 'Todos los permisos',

    // Empty state
    'no_roles_title'                    => 'No hay roles registrados',
    'no_roles_description'              => 'No se encontraron roles que coincidan con la búsqueda.',
    'no_permissions'                    => 'Sin permisos asignados',

    // Row actions & Form modal
    'create_role'                       => 'Crear Rol',
    'edit_role'                         => 'Editar Rol',
    'role_form_description'             => 'Define el nombre del rol y selecciona los permisos correspondientes.',
    'field_role_name'                   => 'Nombre del Rol',
    'field_role_name_placeholder'       => 'Ej. supervisor, auditor, cajero',
    'field_permissions'                 => 'Permisos del Sistema',
    'select_all'                        => 'Seleccionar todos',
    'deselect_all'                      => 'Deseleccionar todos',
    'permissions_selected'              => 'permisos seleccionados',

    // Delete modal
    'delete_role_title'                 => 'Eliminar Rol',
    'delete_role_description'           => '¿Estás seguro de que deseas eliminar este rol? Esta acción no se puede deshacer y los usuarios asignados perderán estos permisos.',

    // Toast notifications
    'role_created'                      => 'Rol creado exitosamente.',
    'role_updated'                      => 'Rol actualizado exitosamente.',
    'role_deleted'                      => 'Rol eliminado exitosamente.',

    // Module permission labels
    'module_dashboard'                  => 'Panel de Control',
    'module_users'                      => 'Usuarios',
    'module_roles'                      => 'Roles',
    'module_categories'                 => 'Categorías',
    'module_products'                   => 'Productos',
    'module_customers'                  => 'Clientes',
    'module_sales'                      => 'Ventas',
    'module_settings'                   => 'Configuración',

    // Navigation Menu
    'nav_dashboard'                     => 'Panel de Control',
    'nav_inventory'                     => 'Inventario',
    'nav_categories'                    => 'Categorías',
    'nav_products'                      => 'Productos',
    'nav_customers'                     => 'Clientes',
    'nav_sales'                         => 'Ventas',
    'nav_reports'                       => 'Reportes',
    'nav_administration'                => 'Administración',
    'nav_users'                         => 'Usuarios',
    'nav_roles'                         => 'Roles',
    'nav_settings'                      => 'Configuración',
    'nav_collapse'                      => 'Plegar',
    'nav_expand'                        => 'Expandir',
    'nav_close_sidebar'                 => 'Cerrar menú lateral',
];
