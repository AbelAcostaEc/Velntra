<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Dashboard" description="Operational snapshot for POS, inventory, customers and sales.">
            <x-slot:actions>
                <x-button variant="secondary">Export</x-button>
                <x-button>New sale</x-button>
            </x-slot:actions>
        </x-page-header>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Today sales" value="$4,280" trend="+12.4% from yesterday" variant="success" />
            <x-stat-card label="Orders" value="128" trend="24 pending" variant="info" />
            <x-stat-card label="Low stock" value="18" trend="Needs review" variant="warning" />
            <x-stat-card label="Customers" value="1,942" trend="+38 this week" variant="success" />
        </div>

        <x-card title="Recent activity" description="Demo content for the administrative foundation.">
            <x-empty-state title="No real activity yet" description="Sprint 02 only defines the UI foundation. Business workflows will connect later." />
        </x-card>
    </div>
</x-app-layout>
