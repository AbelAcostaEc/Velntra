<?php

return [
    // Module & Navigation
    'pos'                               => 'Point of Sale',
    'pos_title'                         => 'Point of Sale (POS)',
    'pos_description'                   => 'Quick billing, barcode scanning, checkout, and hold/resume sales management.',
    'sales_history'                     => 'Sales History',
    'pending_sales'                     => 'Held Sales',

    // Cart & POS Interface
    'cart'                              => 'Sales Cart',
    'empty_cart'                        => 'The cart is empty',
    'empty_cart_help'                   => 'Select products from the catalog or scan a barcode to begin.',
    'clear_cart'                        => 'Clear Cart',
    'hold_sale'                         => 'Hold Sale',
    'resume_sale'                       => 'Resume Sale',
    'delete_held_sale'                  => 'Discard',
    'no_held_sales'                     => 'No held sales',
    'held_sales_title'                  => 'Held / Pending Sales',
    'held_sales_description'            => 'Select a held sale to load it back into the cart and complete checkout.',
    'held_badge'                        => 'on hold',
    'barcode_scan_ready'                => 'Scanner active',

    // Catalog & Search
    'search_products_placeholder'       => 'Search by name, barcode, or SKU...',
    'all_categories'                    => 'All categories',
    'stock_available'                   => 'Available',
    'out_of_stock'                      => 'Out of stock',
    'no_products_found'                 => 'No products found',
    'no_products_description'           => 'Try searching with another keyword or category.',

    // Customer
    'customer'                          => 'Customer',
    'select_customer'                   => 'Select Customer',
    'search_customer_placeholder'       => 'Search customer by name or document...',
    'quick_create_customer'             => 'New Customer',
    'final_consumer'                    => 'Final Consumer',

    // Financial Summary
    'subtotal'                          => 'Subtotal',
    'discount'                          => 'Discount',
    'tax'                               => 'Tax',
    'total'                             => 'Total Due',
    'items_count'                       => 'items',
    'checkout_btn'                      => 'Pay Now',

    // Payment Modal
    'payment_modal_title'               => 'Process Payment',
    'payment_modal_description'         => 'Select payment method and enter the amount tendered by customer.',
    'payment_method'                    => 'Payment Method',
    'cash'                              => 'Cash',
    'card'                              => 'Card',
    'transfer'                          => 'Bank Transfer',
    'mixed'                             => 'Mixed',
    'amount_paid'                       => 'Amount Tendered',
    'change'                            => 'Change Due',
    'exact_amount'                      => 'Exact Amount',
    'complete_sale_btn'                 => 'Complete Sale',
    'processing'                        => 'Processing sale...',
    'notes'                             => 'Notes / Remarks',
    'notes_placeholder'                 => 'Optional order notes...',
    'cancel'                            => 'Cancel',

    // Receipt Modal
    'receipt_title'                     => 'Sales Receipt',
    'sale_number'                       => 'Receipt #',
    'date'                              => 'Date',
    'cashier'                           => 'Cashier / Seller',
    'print_ticket'                      => 'Print Ticket',
    'new_sale'                          => 'New Sale',
    'thank_you_message'                 => 'Thank you for your purchase!',
    'item_name'                         => 'Product',
    'item_qty'                          => 'Qty',
    'item_price'                        => 'Price',
    'item_total'                        => 'Total',

    // History Table & Status
    'status_completed'                  => 'Completed',
    'status_pending'                    => 'On Hold',
    'status_cancelled'                  => 'Cancelled',
    'cancel_sale'                       => 'Cancel Sale',
    'cancel_sale_confirm'               => 'Are you sure you want to cancel this sale? Product inventory will be restored.',
    'col_number'                        => 'Sale #',
    'col_customer'                      => 'Customer',
    'col_date'                          => 'Date & Time',
    'col_total'                         => 'Total',
    'col_payment'                       => 'Method',
    'col_status'                        => 'Status',
    'col_actions'                       => 'Actions',
    'no_sales_title'                    => 'No sales registered',
    'no_sales_description'              => 'Processed sales will appear here.',

    // Notifications
    'sale_completed'                    => 'Sale completed successfully.',
    'sale_held'                         => 'Sale placed on hold successfully.',
    'sale_resumed'                      => 'Sale resumed into active cart.',
    'sale_cancelled'                    => 'Sale cancelled and inventory restored.',
    'held_sale_deleted'                 => 'Held sale discarded.',
    'cart_cleared'                      => 'Cart cleared.',
    'product_added'                     => 'Product added to cart.',
    'stock_limit_reached'               => 'Not enough stock available for this product.',
];
