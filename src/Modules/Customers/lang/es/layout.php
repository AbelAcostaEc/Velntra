<?php

return [
    // Page & Module
    'customers'                          => 'Clientes',
    'customers_management'               => 'Gestión de Clientes',
    'customers_description'              => 'Administra los clientes del negocio, datos de contacto y estado.',

    // Stat cards
    'total_customers'                    => 'Total Clientes',
    'active_customers'                   => 'Clientes Activos',
    'inactive_customers'                 => 'Clientes Inactivos',
    'all_registered'                     => 'Registrados en el sistema',
    'in_operation'                       => 'Habilitados para ventas',
    'temporarily_disabled'               => 'Deshabilitados temporalmente',

    // Table columns & Filters
    'search_placeholder'                 => 'Buscar por nombre, documento, correo o teléfono...',
    'col_document'                       => 'Documento',
    'col_name'                           => 'Nombre / Razón Social',
    'col_phone'                          => 'Teléfono',
    'col_email'                          => 'Correo Electrónico',
    'col_address'                        => 'Dirección',
    'col_status'                         => 'Estado',
    'col_actions'                        => 'Acciones',

    // Common labels
    'active'                             => 'Activo',
    'inactive'                           => 'Inactivo',
    'all_statuses'                       => 'Todos los estados',
    'no_document'                        => 'Sin documento',
    'no_phone'                           => 'Sin teléfono',
    'no_email'                           => 'Sin correo',
    'no_address'                         => 'Sin dirección',
    'final_consumer_badge'               => 'Consumidor Final',

    // Empty state
    'no_customers_title'                 => 'No hay clientes registrados',
    'no_customers_description'           => 'No se encontraron clientes que coincidan con los criterios de búsqueda.',

    // Actions & Form
    'create_customer'                    => 'Crear Cliente',
    'edit_customer'                      => 'Editar Cliente',
    'form_description'                   => 'Completa la información del cliente a continuación.',
    'field_document'                     => 'Documento (Cédula / RUC)',
    'field_document_placeholder'         => 'Ej. 0999999999 o 9999999999999',
    'field_name'                         => 'Nombre Completo',
    'field_name_placeholder'             => 'Ej. Juan Pérez o Distribuidora S.A.',
    'field_phone'                        => 'Teléfono / Celular',
    'field_phone_placeholder'            => 'Ej. 0987654321',
    'field_email'                        => 'Correo Electrónico',
    'field_email_placeholder'            => 'cliente@ejemplo.com',
    'field_address'                      => 'Dirección',
    'field_address_placeholder'          => 'Ej. Av. Principal 123 y Secundaria',
    'field_is_active'                    => 'Cliente Activo',
    'field_is_active_description'        => 'Determina si el cliente está disponible para registrar ventas.',
    'edit'                               => 'Editar',
    'delete'                             => 'Eliminar',
    'cancel'                             => 'Cancelar',
    'save'                               => 'Guardar',
    'saving'                             => 'Guardando...',

    // Purchase History Page
    'purchases_history'                  => 'Historial de Compras',
    'customer_history_title'             => 'Historial de Compras del Cliente',
    'customer_history_description'       => 'Consulta el registro detallado de todas las transacciones, compras y comprobantes de este cliente.',
    'back_to_customers'                  => 'Volver a Clientes',
    'total_spent'                        => 'Total Comprado',
    'total_purchases'                    => 'Compras Realizadas',
    'average_ticket'                     => 'Ticket Promedio',
    'last_purchase'                      => 'Última Compra',
    'no_purchases_yet'                   => 'Sin compras aún',
    'completed_sales_only'               => 'Ventas completadas',
    'search_sales_placeholder'           => 'Buscar por número de comprobante...',
    'col_sale_number'                    => 'N° Comprobante',
    'col_sale_date'                      => 'Fecha y Hora',
    'col_sale_items'                     => 'Ítems',
    'col_sale_payment'                   => 'Método de Pago',
    'col_sale_total'                     => 'Total',
    'col_sale_status'                    => 'Estado',
    'view_receipt'                       => 'Ver Comprobante',
    'no_sales_found'                     => 'No se encontraron compras',
    'no_sales_found_description'         => 'Este cliente no cuenta con compras registradas o no coinciden con los filtros aplicados.',

    // Delete modal
    'delete_customer_title'              => 'Eliminar Cliente',
    'delete_customer_description'        => '¿Estás seguro de que deseas eliminar este cliente? Esta acción no se puede deshacer.',
    'cannot_delete_final_consumer'       => 'El cliente Consumidor Final no puede ser eliminado.',

    // Toast notifications
    'customer_created'                   => 'Cliente creado exitosamente.',
    'customer_updated'                   => 'Cliente actualizado exitosamente.',
    'customer_deleted'                   => 'Cliente eliminado exitosamente.',
    'customer_status_updated'            => 'Estado del cliente actualizado exitosamente.',
];
