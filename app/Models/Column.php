<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    /** @use HasFactory<\Database\Factories\ColumnFactory> */
    use HasFactory, Sortable;

    protected $fillable = [
        'board_id',
        'title',
    ];
    
    public function sortableAmongst()
    {
        return static::where('board_id', $this->board_id);
    }

    public function items()
    {
        return $this->hasMany(Item::class)->orderBy('position');
    }
}
