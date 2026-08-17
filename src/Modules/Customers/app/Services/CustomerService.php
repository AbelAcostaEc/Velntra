<?php

namespace Modules\Customers\Services;

// Framework & Database
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

// Models
use Modules\Customers\Models\Customer;

class CustomerService
{
    /**
     * Buscar un cliente por su ID o lanzar una excepción ModelNotFoundException.
     *
     * @param int $id
     * @return Customer
     */
    public function find(int $id): Customer
    {
        return Customer::findOrFail($id);
    }

    /**
     * Crear un nuevo cliente dentro de una transacción de BD.
     *
     * @param array{
     *     document?: string|null,
     *     name: string,
     *     phone?: string|null,
     *     email?: string|null,
     *     address?: string|null,
     *     is_active?: bool
     * } $data
     * @return Customer
     */
    public function create(array $data): Customer
    {
        $document = !empty($data['document']) ? trim($data['document']) : null;

        if ($document) {
            $existing = Customer::where('document', $document)->first();
            if ($existing) {
                throw new InvalidArgumentException("Ya existe un cliente activo registrado con el documento {$document}.");
            }
        }

        return DB::transaction(function () use ($data, $document) {
            return Customer::create([
                'document'  => $document,
                'name'      => trim($data['name']),
                'phone'     => !empty($data['phone']) ? trim($data['phone']) : null,
                'email'     => !empty($data['email']) ? trim($data['email']) : null,
                'address'   => !empty($data['address']) ? trim($data['address']) : null,
                'is_active' => $data['is_active'] ?? true,
            ]);
        });
    }

    /**
     * Actualizar datos de un cliente existente dentro de una transacción de BD.
     *
     * @param Customer $customer
     * @param array{
     *     document?: string|null,
     *     name: string,
     *     phone?: string|null,
     *     email?: string|null,
     *     address?: string|null,
     *     is_active?: bool
     * } $data
     * @return Customer
     */
    public function update(Customer $customer, array $data): Customer
    {
        $document = !empty($data['document']) ? trim($data['document']) : null;

        if ($document) {
            $existing = Customer::where('document', $document)
                ->where('id', '!=', $customer->id)
                ->first();
            if ($existing) {
                throw new InvalidArgumentException("Ya existe un cliente activo registrado con el documento {$document}.");
            }
        }

        return DB::transaction(function () use ($customer, $data, $document) {
            $customer->update([
                'document'  => $document,
                'name'      => trim($data['name']),
                'phone'     => !empty($data['phone']) ? trim($data['phone']) : null,
                'email'     => !empty($data['email']) ? trim($data['email']) : null,
                'address'   => !empty($data['address']) ? trim($data['address']) : null,
                'is_active' => $data['is_active'] ?? $customer->is_active,
            ]);

            return $customer;
        });
    }

    /**
     * Eliminar un cliente de forma segura dentro de una transacción de BD.
     * Protege al cliente 'Consumidor Final' de ser eliminado (Regla BR-018).
     *
     * @param Customer $customer
     * @return bool
     * @throws InvalidArgumentException
     */
    public function delete(Customer $customer): bool
    {
        if ($customer->isConsumidorFinal()) {
            throw new InvalidArgumentException('El cliente Consumidor Final no puede ser eliminado.');
        }

        return DB::transaction(function () use ($customer) {
            return (bool) $customer->delete();
        });
    }

    /**
     * Alternar el estado activo/inactivo de un cliente.
     *
     * @param Customer $customer
     * @return Customer
     */
    public function toggleStatus(Customer $customer): Customer
    {
        return DB::transaction(function () use ($customer) {
            $customer->update([
                'is_active' => !$customer->is_active,
            ]);

            return $customer;
        });
    }
}
