<?php

return [
    // Module & Navigation
    'pos'                               => 'Punto de Venta',
    'pos_title'                         => 'Punto de Venta (POS)',
    'pos_description'                   => 'Facturación rápida, escaneo de productos, cobro y gestión de ventas en espera.',
    'sales_history'                     => 'Historial de Ventas',
    'pending_sales'                     => 'Ventas en Espera',

    // Cart & POS Interface
    'cart'                              => 'Carrito de Venta',
    'empty_cart'                        => 'El carrito está vacío',
    'empty_cart_help'                   => 'Selecciona productos del catálogo o escribe el código para agregarlos.',
    'clear_cart'                        => 'Vaciar Carrito',
    'hold_sale'                         => 'Poner en Espera',
    'resume_sale'                       => 'Reanudar Venta',
    'delete_held_sale'                  => 'Descartar',
    'no_held_sales'                     => 'No hay ventas en espera',
    'held_sales_title'                  => 'Ventas en Espera',
    'held_sales_description'            => 'Selecciona una venta pausada para cargarla al carrito y concretar el cobro.',
    'held_badge'                        => 'en espera',
    'barcode_scan_ready'                => 'Lector activo',

    // Catalog & Search
    'search_products_placeholder'       => 'Buscar por nombre, código de barras o SKU...',
    'all_categories'                    => 'Todas las categorías',
    'stock_available'                   => 'Disponibles',
    'out_of_stock'                      => 'Sin stock',
    'no_products_found'                 => 'No se encontraron productos',
    'no_products_description'           => 'Intenta con otro término de búsqueda o categoría.',

    // Customer
    'customer'                          => 'Cliente',
    'select_customer'                   => 'Seleccionar Cliente',
    'search_customer_placeholder'       => 'Buscar cliente por nombre o cédula/RUC...',
    'quick_create_customer'             => 'Nuevo Cliente',
    'final_consumer'                    => 'Consumidor Final',

    // Financial Summary
    'subtotal'                          => 'Subtotal',
    'discount'                          => 'Descuento',
    'tax'                               => 'IVA',
    'total'                             => 'Total a Pagar',
    'items_count'                       => 'ítems',
    'checkout_btn'                      => 'Cobrar',

    // Payment Modal
    'payment_modal_title'               => 'Procesar Cobro',
    'payment_modal_description'         => 'Selecciona el método de pago y registra el importe entregado por el cliente.',
    'payment_method'                    => 'Método de Pago',
    'cash'                              => 'Efectivo',
    'card'                              => 'Tarjeta',
    'transfer'                          => 'Transferencia',
    'mixed'                             => 'Mixto',
    'amount_paid'                       => 'Monto Recibido',
    'change'                            => 'Cambio / Vuelto',
    'exact_amount'                      => 'Monto Exacto',
    'complete_sale_btn'                 => 'Completar Venta',
    'processing'                        => 'Procesando venta...',
    'notes'                             => 'Notas u observaciones',
    'notes_placeholder'                 => 'Opcional: nota del pedido...',
    'cancel'                            => 'Cancelar',

    // Receipt Modal
    'receipt_title'                     => 'Comprobante de Venta',
    'sale_number'                       => 'N° Comprobante',
    'date'                              => 'Fecha',
    'cashier'                           => 'Cajero / Vendedor',
    'print_ticket'                      => 'Imprimir Ticket',
    'new_sale'                          => 'Nueva Venta',
    'thank_you_message'                 => '¡Gracias por su compra!',
    'item_name'                         => 'Producto',
    'item_qty'                          => 'Cant.',
    'item_price'                        => 'Precio',
    'item_total'                        => 'Total',

    // History Table & Status
    'status_completed'                  => 'Completada',
    'status_pending'                    => 'En Espera',
    'status_cancelled'                  => 'Anulada',
    'cancel_sale'                       => 'Anular Venta',
    'cancel_sale_confirm'               => '¿Estás seguro de anular esta venta? El stock de los productos será restaurado.',
    'col_number'                        => 'N° Venta',
    'col_customer'                      => 'Cliente',
    'col_date'                          => 'Fecha y Hora',
    'col_total'                         => 'Total',
    'col_payment'                       => 'Método',
    'col_status'                        => 'Estado',
    'col_actions'                       => 'Acciones',
    'no_sales_title'                    => 'No hay ventas registradas',
    'no_sales_description'              => 'Las ventas realizadas aparecerán listadas aquí.',

    // Notifications
    'sale_completed'                    => 'Venta completada exitosamente.',
    'sale_held'                         => 'Venta guardada en espera correctamente.',
    'sale_resumed'                      => 'Venta reanudada en el carrito.',
    'sale_cancelled'                    => 'Venta anulada y stock restaurado.',
    'held_sale_deleted'                 => 'Venta en espera descartada.',
    'cart_cleared'                      => 'Carrito vaciado.',
    'product_added'                     => 'Producto agregado al carrito.',
    'stock_limit_reached'               => 'No hay suficiente stock disponible para este producto.',
];
