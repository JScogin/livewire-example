<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Board;

new class extends Component
{
    public Board $board;

    public function mount()
    {
        $this->board = Board::with('columns.items')->first();
    }

    public function moveColumn($item, $position)
    {
        $column = $this->board->columns()->findOrFail($item);

        $column->move($position);
    }
};
?>

<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">{{ $this->board->title }}</h1>
    
    <div class="flex gap-4 overflow-x-auto">
        @foreach ($this->board->columns as $column)
            <livewire:pages::column
                :column="$column"
                wire:key="{{$column->id}}"
            />
        @endforeach
    </div>
</div>