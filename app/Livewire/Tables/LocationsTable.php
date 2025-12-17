<?php

namespace App\Livewire\Tables;

use App\Models\Location;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class LocationsTable extends PowerGridComponent
{
    public string $tableName = 'locationsTable';

    public function setUp(): array
    {
        // $this->showCheckBox();
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
        return Location::query();
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
            ->add('company_name')
            ->add('address')
            ->add('city')
            ->add('state')
            ->add('zipcode')
            ->add('business_type', function ($location) {
                return ucfirst($location->business_type);
            })
            ->add('margin')
            ->add('status', function ($location) {
                if ($location->status) {
                    return "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'>Active</span>";
                } else {
                    return "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'>Inactive</span>";
                }
            })
            ->add('created_at')
            ->add('created_at_formatted', fn($location) => Carbon::parse($location->created_at)->format('d/m/Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            // Column::make('Company', 'company_name')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Phone', 'phone')
                ->sortable()
                ->searchable(),

            Column::make('City', 'city')
                ->sortable()
                ->searchable(),

            Column::make('State', 'state')
                ->sortable()
                ->searchable(),

            Column::make('Zipcode', 'zipcode')
                ->sortable()
                ->searchable(),

            Column::make('Business Type', 'business_type')
                ->sortable()
                ->searchable(),

            Column::make('Margin %', 'margin')
                ->sortable(),

            Column::make('Status', 'status')
                ->bodyAttribute('raw'),

            Column::make('Created at',  'created_at'),


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
        $this->dispatch('load::location', location: $rowId);
    }

    #[\Livewire\Attributes\On('manage_payment_methods')]
    public function managePaymentMethods($rowId): void
    {
        $this->dispatch('load::location_payment_methods', location: $rowId);
    }


    public function actions(Location $row): array
    {
        return [
            Button::add('edit')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->tooltip("Edit Location")
                ->dispatch('edit', ['rowId' => $row->id]),

            // Payment Methods
            Button::add('payment_methods')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m4 0h1M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>')
                ->id()
                ->tooltip("Manage Payment Methods")
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('manage_payment_methods', ['rowId' => $row->id])
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
