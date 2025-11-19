<?php

use Livewire\Component;

new class extends Component
{
    public $column;
};
?>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 min-w-[300px] flex flex-col">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">{{ $this->column->title }}</h2>
    </div>
    
    <div class="p-4 flex-1 space-y-2">
        @foreach ($this->column->items as $item)
            <div class="bg-gray-50 p-3 rounded border border-gray-200">
                <p class="text-sm text-gray-700">{{ $item->title }}</p>
            </div>
        @endforeach
        
        @if($this->column->items->isEmpty())
            <p class="text-sm text-gray-400 text-center py-4">No items</p>
        @endif
    </div>
</div>