<?php

namespace Modules\Customers\Livewire\Customers;

// Framework & Livewire
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// Models
use Modules\Customers\Models\Customer;

// Services
use Modules\Customers\Services\CustomerService;

#[Layout('layouts.app')]
class CustomerIndex extends Component
{
    use WithPagination;

    /**
     * Término de búsqueda para filtrar clientes por nombre, documento, email o teléfono.
     */
    public string $search = '';

    /**
     * Filtro por estado activo/inactivo ('', 'active', 'inactive').
     */
    public string $statusFilter = '';

    /**
     * Cantidad de registros por página en la tabla.
     */
    public int $perPage = 10;

    /**
     * ID del cliente seleccionado para edición o eliminación (null en creación).
     */
    public ?int $selectedCustomerId = null;

    /**
     * Documento de identificación del cliente (Cédula / RUC).
     */
    public ?string $document = '';

    /**
     * Nombre o razón social del cliente.
     */
    public string $name = '';

    /**
     * Teléfono o número de celular del cliente.
     */
    public ?string $phone = '';

    /**
     * Correo electrónico del cliente.
     */
    public ?string $email = '';

    /**
     * Dirección física o domicilio del cliente.
     */
    public ?string $address = '';

    /**
     * Estado activo o inactivo del cliente.
     */
    public bool $is_active = true;

    /**
     * Nombre del cliente en proceso de eliminación (para mensaje de confirmación).
     */
    public string $deletingCustomerName = '';

    /**
     * Reiniciar la paginación al cambiar el término de búsqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reiniciar la paginación al cambiar el filtro de estado.
     */
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Reiniciar la paginación al cambiar la cantidad de items por página.
     */
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Reglas de validación dinámicas para la creación y edición de clientes.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'document'  => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('customers', 'document')
                    ->ignore($this->selectedCustomerId)
                    ->whereNull('deleted_at'),
            ],
            'name'      => ['required', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:30'],
            'email'     => ['nullable', 'email', 'max:255'],
            'address'   => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Nombres de atributos personalizados para los mensajes de validación.
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'document'  => __t('field_document', 'customers'),
            'name'      => __t('field_name', 'customers'),
            'phone'     => __t('field_phone', 'customers'),
            'email'     => __t('field_email', 'customers'),
            'address'   => __t('field_address', 'customers'),
            'is_active' => __t('field_is_active', 'customers'),
        ];
    }

    /**
     * Preparar el formulario para crear un nuevo cliente y verificar permisos.
     */
    public function openCreateModal(): void
    {
        $this->authorize('create', Customer::class);

        $this->reset([
            'selectedCustomerId',
            'document',
            'name',
            'phone',
            'email',
            'address',
            'deletingCustomerName',
        ]);
        $this->is_active = true;
        $this->resetValidation();
    }

    /**
     * Cargar los datos de un cliente existente para edición y verificar permisos.
     */
    public function openEditModal(int $customerId, CustomerService $customerService): void
    {
        $customer = $customerService->find($customerId);

        $this->authorize('update', $customer);

        $this->selectedCustomerId = $customer->id;
        $this->document = $customer->document ?? '';
        $this->name = $customer->name;
        $this->phone = $customer->phone ?? '';
        $this->email = $customer->email ?? '';
        $this->address = $customer->address ?? '';
        $this->is_active = (bool) $customer->is_active;
        $this->resetValidation();
    }

    /**
     * Seleccionar el cliente que se desea eliminar y verificar permisos.
     */
    public function openDeleteModal(int $customerId, CustomerService $customerService): void
    {
        $customer = $customerService->find($customerId);

        $this->authorize('delete', $customer);

        $this->selectedCustomerId = $customer->id;
        $this->deletingCustomerName = $customer->name;
    }

    /**
     * Guardar el cliente (crear nuevo o actualizar existente) mediante CustomerService.
     */
    public function save(CustomerService $customerService): void
    {
        $this->validate();

        $customerData = [
            'document'  => $this->document,
            'name'      => $this->name,
            'phone'     => $this->phone,
            'email'     => $this->email,
            'address'   => $this->address,
            'is_active' => $this->is_active,
        ];

        if ($this->selectedCustomerId) {
            // Edición de cliente existente
            $customer = $customerService->find($this->selectedCustomerId);
            $this->authorize('update', $customer);

            $customerService->update($customer, $customerData);

            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('customer_updated', 'customers'),
            ]);
        } else {
            // Creación de nuevo cliente
            $this->authorize('create', Customer::class);

            $customerService->create($customerData);

            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('customer_created', 'customers'),
            ]);
        }

        $this->reset([
            'selectedCustomerId',
            'document',
            'name',
            'phone',
            'email',
            'address',
            'deletingCustomerName',
        ]);
        $this->is_active = true;
        $this->dispatch('close-modal', 'customer-form');
    }

    /**
     * Eliminar el cliente seleccionado mediante CustomerService.
     */
    public function delete(CustomerService $customerService): void
    {
        if ($this->selectedCustomerId) {
            $customer = $customerService->find($this->selectedCustomerId);
            $this->authorize('delete', $customer);

            $customerService->delete($customer);

            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => __t('customer_deleted', 'customers'),
            ]);
        }

        $this->reset(['selectedCustomerId', 'deletingCustomerName']);
        $this->dispatch('close-modal', 'delete-customer');
    }

    /**
     * Alternar rápidamente el estado activo/inactivo de un cliente.
     */
    public function toggleStatus(int $id, CustomerService $customerService): void
    {
        $customer = $customerService->find($id);
        $this->authorize('update', $customer);

        $customerService->toggleStatus($customer);

        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => __t('customer_status_updated', 'customers'),
        ]);
    }

    /**
     * Renderizar la vista del listado de clientes y estadísticas con verificación de acceso.
     */
    public function render(): View
    {
        // Autorizar visualización general del listado
        $this->authorize('viewAny', Customer::class);

        $query = Customer::query()
            ->search($this->search);

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $customers = $query->latest('id')->paginate($this->perPage);

        return view('customers::livewire.customers.customer-index', [
            'customers'         => $customers,
            'totalCustomers'    => Customer::count(),
            'activeCustomers'   => Customer::where('is_active', true)->count(),
            'inactiveCustomers' => Customer::where('is_active', false)->count(),
        ]);
    }
}
