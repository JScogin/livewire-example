<?php

namespace Tests\Unit\Services;

use App\Models\Widget;
use App\Services\WidgetService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WidgetServiceTest extends TestCase
{
    use RefreshDatabase;

    private WidgetService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WidgetService();
    }

    public function test_service_can_list_widgets()
    {
        Widget::factory()->count(5)->create();

        $result = $this->service->list();

        $this->assertCount(5, $result->items());
    }

    public function test_service_can_filter_by_status()
    {
        Widget::factory()->active()->count(3)->create();
        Widget::factory()->inactive()->count(2)->create();

        $result = $this->service->list(['status' => 'active']);

        $this->assertCount(3, $result->items());
        $this->assertTrue($result->every(fn ($widget) => $widget->status === 'active'));
    }

    public function test_service_can_filter_by_price_range()
    {
        Widget::factory()->create(['price' => 10.00]);
        Widget::factory()->create(['price' => 25.00]);
        Widget::factory()->create(['price' => 50.00]);
        Widget::factory()->create(['price' => 100.00]);

        $result = $this->service->list([
            'min_price' => 20.00,
            'max_price' => 75.00,
        ]);

        $this->assertCount(2, $result->items());
    }

    public function test_service_can_filter_by_quantity_range()
    {
        Widget::factory()->create(['quantity' => 10]);
        Widget::factory()->create(['quantity' => 25]);
        Widget::factory()->create(['quantity' => 50]);
        Widget::factory()->create(['quantity' => 100]);

        $result = $this->service->list([
            'min_quantity' => 20,
            'max_quantity' => 75,
        ]);

        $this->assertCount(2, $result->items());
    }

    public function test_service_can_search_widgets()
    {
        Widget::factory()->create([
            'name' => 'Premium Widget',
            'description' => 'A high-quality widget',
        ]);

        Widget::factory()->create([
            'name' => 'Basic Widget',
            'description' => 'A simple widget',
        ]);

        $result = $this->service->list(['search' => 'premium']);

        $this->assertCount(1, $result->items());
        $this->assertEquals('Premium Widget', $result->items()[0]->name);
    }

    public function test_service_can_sort_widgets()
    {
        Widget::factory()->create(['name' => 'Zebra Widget']);
        Widget::factory()->create(['name' => 'Alpha Widget']);
        Widget::factory()->create(['name' => 'Beta Widget']);

        $result = $this->service->list([
            'sort' => 'name',
            'direction' => 'asc',
        ]);

        $this->assertEquals('Alpha Widget', $result->items()[0]->name);
        $this->assertEquals('Beta Widget', $result->items()[1]->name);
        $this->assertEquals('Zebra Widget', $result->items()[2]->name);
    }

    public function test_service_can_paginate_widgets()
    {
        Widget::factory()->count(25)->create();

        $result = $this->service->list([], 10);

        $this->assertCount(10, $result->items());
        $this->assertEquals(25, $result->total());
        $this->assertEquals(3, $result->lastPage());
    }

    public function test_service_can_create_widget()
    {
        $data = [
            'name' => 'Test Widget',
            'description' => 'Test Description',
            'price' => 29.99,
            'quantity' => 50,
            'status' => 'active',
        ];

        $widget = $this->service->create($data);

        $this->assertInstanceOf(Widget::class, $widget);
        $this->assertEquals('Test Widget', $widget->name);
        $this->assertEquals(29.99, $widget->price);
        $this->assertDatabaseHas('widgets', ['name' => 'Test Widget']);
    }

    public function test_service_can_update_widget()
    {
        $widget = Widget::factory()->create(['name' => 'Original Name']);

        $updated = $this->service->update($widget->id, [
            'name' => 'Updated Name',
            'price' => 39.99,
        ], false);

        $this->assertEquals('Updated Name', $updated->name);
        $this->assertEquals(39.99, $updated->price);
    }

    public function test_service_can_partially_update_widget()
    {
        $widget = Widget::factory()->create([
            'name' => 'Original Name',
            'price' => 29.99,
        ]);

        $updated = $this->service->update($widget->id, [
            'price' => 39.99,
        ], true);

        $this->assertEquals('Original Name', $updated->name);
        $this->assertEquals(39.99, $updated->price);
    }

    public function test_service_throws_exception_for_nonexistent_id()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->find(999);
    }

    public function test_service_can_soft_delete()
    {
        $widget = Widget::factory()->create();

        $this->service->delete($widget->id);

        $this->assertSoftDeleted('widgets', ['id' => $widget->id]);
        $this->assertNull(Widget::find($widget->id));
    }

    public function test_service_validates_unique_name_on_create()
    {
        Widget::factory()->create(['name' => 'Unique Widget']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $this->service->create(['name' => 'Unique Widget']);
    }

    public function test_service_find_returns_widget()
    {
        $widget = Widget::factory()->create();

        $found = $this->service->find($widget->id);

        $this->assertInstanceOf(Widget::class, $found);
        $this->assertEquals($widget->id, $found->id);
    }
}

