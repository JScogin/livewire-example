<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Column;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColumnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $board = Board::where('title', 'My Board')->first();

        if ($board) {
            $columns = ['Backlog', 'To Do', 'In Progress'];

            foreach ($columns as $title) {
                Column::firstOrCreate(
                    [
                        'board_id' => $board->id,
                        'title' => $title,
                    ]
                );
            }
        }
    }
}
