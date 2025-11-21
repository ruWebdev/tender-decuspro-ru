<?php

namespace Database\Seeders;

use App\Models\Tender;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(UiContentSeeder::class);

        // Базовые пользователи с ролями
        $customer = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
        ]);
        $customer->assignRole(User::ROLE_CUSTOMER);

        $supplier = User::factory()->create([
            'name' => 'Supplier User',
            'email' => 'supplier@example.com',
        ]);
        $supplier->assignRole(User::ROLE_SUPPLIER);

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole(User::ROLE_ADMIN);

        // Тестовый тендер
        $tender = Tender::create([
            'customer_id' => $customer->id,
            'title' => 'Закупка офисных принадлежностей',
            'description' => 'Закупка бумаги, канцтоваров и прочих офисных принадлежностей.',
            'hidden_comment' => null,
            'valid_until' => now()->addDays(7),
        ]);

        $tender->items()->createMany([
            [
                'title' => 'Бумага офисная A4',
                'quantity' => 10,
                'unit' => 'уп.',
                'position_index' => 0,
            ],
            [
                'title' => 'Ручка шариковая синяя',
                'quantity' => 50,
                'unit' => 'шт.',
                'position_index' => 1,
            ],
        ]);
    }
}
