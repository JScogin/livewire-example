<?php

namespace Database\Seeders;

use App\Models\Column;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $columns = Column::all();

        foreach ($columns as $column) {
            $itemCount = rand(3, 5);

            for ($i = 0; $i < $itemCount; $i++) {
                Item::create([
                    'column_id' => $column->id,
                    'title' => "Item " . ($i + 1) . " in {$column->title}",
                ]);
            }
        }
    }
}
