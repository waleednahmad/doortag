<?php

namespace App\Livewire\Tables;

use App\Models\Customer;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class CustomersTable extends PowerGridComponent
{
    public string $tableName = 'customersTable';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    #[On('created')]
    #[On('updated')]
    public function datasource(): Builder
    {
        return Customer::query()->with('location');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('phone')
            ->add('location_name', fn($customer) => $customer->location?->company_name ?? 'N/A')
            ->add('is_admin', function ($customer) {
                if ($customer->is_admin) {
                    return "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800'>Admin</span>";
                } else {
                    return "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800'>User</span>";
                }
            })
            ->add('can_modify_data', function ($customer) {
                if ($customer->can_modify_data) {
                    return "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'>Yes</span>";
                } else {
                    return "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'>No</span>";
                }
            })
            ->add('created_at')
            ->add('created_at_formatted', fn($customer) => Carbon::parse($customer->created_at)->format('d/m/Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Phone', 'phone')
                ->sortable()
                ->searchable(),

            Column::make('Location', 'location_name'),

            Column::make('Is Admin', 'is_admin'),

            Column::make('Can Modify Data', 'can_modify_data'),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->dispatch('load::customer', customer: $rowId);
    }

    public function actions(Customer $row): array
    {
        return [
            Button::add('edit')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }
}
