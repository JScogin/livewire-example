<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

trait Sortable
{
    public static function bootSortable()
    {
        static::creating(function ($model) {
            $model->position = $model->sortableAmongst()->max('position') + 1;
        });

        static::deleting(function ($model) {
            $model->move(9999);
        });
    }

    public function move($position)
    {
        $position = $position +1;

        DB::transaction(function () use ($position) {
            $current = $this->position;

            if ($current === $position) {
                return;
            }

            $direction = $current < $position ? 'up' : 'down';

            if ($direction === 'up') {
                $this->sortableAmongst()->whereBetween('position', [$current, $position - 1])
                    ->where('id', '!=', $this->id)
                    ->increment('position');
            } else {
                $this->sortableAmongst()->whereBetween('position', [$position + 1, $current])
                    ->where('id', '!=', $this->id)
                    ->decrement('position');
            }

            $this->position = $position;

            $this->save();
        });
    }
}