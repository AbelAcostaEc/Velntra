<?php

return [
    // --- USERS ---
    // Page
    'users'                             => 'Users',
    'users_management'                  => 'User management',
    'users_management_description'      => 'Manage system users and their access roles.',

    // Stat cards
    'total_users'                       => 'Total Users',
    'registered'                        => 'Registered',
    'admins'                            => 'Administrators',
    'full_access'                       => 'Full Access',
    'sellers'                           => 'Sellers',
    'operational_access'                => 'Operational Access',

    // Table
    'search_placeholder'                => 'Search by name or email...',
    'col_name'                          => 'Name',
    'col_email'                         => 'Email',
    'col_role'                          => 'Role',
    'col_actions'                       => 'Actions',

    // Empty state
    'no_users_title'                    => 'No users found',
    'no_users_description'              => 'No users matched your search criteria.',
    'no_role'                           => 'No role',

    // Row actions
    'edit'                              => 'Edit',
    'delete'                            => 'Delete',
    'create_user'                       => 'Create User',

    // Form modal
    'edit_user'                         => 'Edit User',
    'form_description'                  => 'Fill in the user information below.',
    'field_name'                        => 'Full Name',
    'field_name_placeholder'            => 'e.g. John Doe',
    'field_email'                       => 'Email Address',
    'field_password'                    => 'Password',
    'field_password_edit_placeholder'   => 'Leave blank to keep current password',
    'field_password_create_placeholder' => 'Minimum 8 characters',
    'field_role'                        => 'Access Role',
    'cancel'                            => 'Cancel',
    'save'                              => 'Save',
    'saving'                            => 'Saving...',

    // Delete modal
    'delete_user_title'                 => 'Delete User',
    'delete_user_description'           => 'Are you sure you want to delete this user? This action cannot be undone.',

    // Toast notifications
    'user_created'                      => 'User created successfully.',
    'user_updated'                      => 'User updated successfully.',
    'user_deleted'                      => 'User deleted successfully.',

    // --- ROLES ---
    // Page
    'roles'                             => 'Roles',
    'roles_management'                  => 'Role management',
    'roles_management_description'      => 'Manage system roles and their assigned permissions.',

    // Stat cards
    'total_roles'                       => 'Total Roles',
    'total_permissions'                 => 'Total Permissions',
    'assigned_users'                    => 'Users with Role',
    'configured_roles'                  => 'Configured Roles',
    'available_permissions'             => 'Available Permissions',
    'active_assignments'                => 'Active Assignments',

    // Table
    'search_roles_placeholder'          => 'Search role by name...',
    'col_permissions'                   => 'Assigned Permissions',
    'col_users'                         => 'Assigned Users',
    'permissions_count'                 => 'Permissions',
    'users_count'                       => 'Users',
    'all_permissions'                   => 'All permissions',

    // Empty state
    'no_roles_title'                    => 'No roles found',
    'no_roles_description'              => 'No roles matched your search criteria.',
    'no_permissions'                    => 'No permissions assigned',

    // Row actions & Form modal
    'create_role'                       => 'Create Role',
    'edit_role'                         => 'Edit Role',
    'role_form_description'             => 'Define the role name and select the corresponding permissions.',
    'field_role_name'                   => 'Role Name',
    'field_role_name_placeholder'       => 'e.g. supervisor, auditor, cashier',
    'field_permissions'                 => 'System Permissions',
    'select_all'                        => 'Select all',
    'deselect_all'                      => 'Deselect all',
    'permissions_selected'              => 'permissions selected',

    // Delete modal
    'delete_role_title'                 => 'Delete Role',
    'delete_role_description'           => 'Are you sure you want to delete this role? This action cannot be undone and assigned users will lose these permissions.',

    // Toast notifications
    'role_created'                      => 'Role created successfully.',
    'role_updated'                      => 'Role updated successfully.',
    'role_deleted'                      => 'Role deleted successfully.',

    // Module permission labels
    'module_dashboard'                  => 'Dashboard',
    'module_users'                      => 'Users',
    'module_roles'                      => 'Roles',
    'module_categories'                 => 'Categories',
    'module_products'                   => 'Products',
    'module_customers'                  => 'Customers',
    'module_sales'                      => 'Sales',
    'module_settings'                   => 'Settings',

    // Navigation Menu
    'nav_dashboard'                     => 'Dashboard',
    'nav_inventory'                     => 'Inventory',
    'nav_categories'                    => 'Categories',
    'nav_products'                      => 'Products',
    'nav_customers'                     => 'Customers',
    'nav_sales'                         => 'Sales',
    'nav_reports'                       => 'Reports',
    'nav_administration'                => 'Administration',
    'nav_users'                         => 'Users',
    'nav_roles'                         => 'Roles',
    'nav_settings'                      => 'Settings',
    'nav_collapse'                      => 'Collapse',
    'nav_expand'                        => 'Expand',
    'nav_close_sidebar'                 => 'Close sidebar',
];
