<?php

namespace Tests\Unit\Models;

use App\Models\Widget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_has_fillable_fields()
    {
        $widget = new Widget();
        $fillable = $widget->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('price', $fillable);
        $this->assertContains('quantity', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('metadata', $fillable);
    }

    public function test_widget_casts_price_to_decimal()
    {
        $widget = Widget::factory()->create([
            'price' => 29.99,
        ]);

        $this->assertIsFloat($widget->price);
        $this->assertEquals(29.99, $widget->price);
    }

    public function test_widget_casts_metadata_to_array()
    {
        $metadata = ['color' => 'blue', 'size' => 'large'];
        $widget = Widget::factory()->create([
            'metadata' => $metadata,
        ]);

        $this->assertIsArray($widget->metadata);
        $this->assertEquals($metadata, $widget->metadata);
    }

    public function test_widget_has_soft_deletes()
    {
        $widget = Widget::factory()->create();
        $widgetId = $widget->id;

        $widget->delete();

        $this->assertSoftDeleted('widgets', ['id' => $widgetId]);
        $this->assertNull(Widget::find($widgetId));
        $this->assertNotNull(Widget::withTrashed()->find($widgetId));
    }

    public function test_active_scope_filters_active_widgets()
    {
        Widget::factory()->active()->create(['name' => 'Active Widget']);
        Widget::factory()->inactive()->create(['name' => 'Inactive Widget']);
        Widget::factory()->archived()->create(['name' => 'Archived Widget']);

        $activeWidgets = Widget::active()->get();

        $this->assertCount(1, $activeWidgets);
        $this->assertEquals('Active Widget', $activeWidgets->first()->name);
    }

    public function test_search_scope_filters_by_name_or_description()
    {
        Widget::factory()->create([
            'name' => 'Premium Widget',
            'description' => 'A high-quality widget',
        ]);

        Widget::factory()->create([
            'name' => 'Basic Widget',
            'description' => 'A simple widget',
        ]);

        Widget::factory()->create([
            'name' => 'Standard Widget',
            'description' => 'A premium quality widget',
        ]);

        $results = Widget::search('premium')->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('name', 'Premium Widget'));
        $this->assertTrue($results->contains('description', 'A premium quality widget'));
    }

    public function test_price_range_scope_filters_by_price()
    {
        Widget::factory()->create(['price' => 10.00]);
        Widget::factory()->create(['price' => 25.00]);
        Widget::factory()->create(['price' => 50.00]);
        Widget::factory()->create(['price' => 100.00]);

        $results = Widget::priceRange(20.00, 75.00)->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('price', 25.00));
        $this->assertTrue($results->contains('price', 50.00));
    }

    public function test_status_label_accessor_returns_correct_label()
    {
        $activeWidget = Widget::factory()->active()->create();
        $inactiveWidget = Widget::factory()->inactive()->create();
        $archivedWidget = Widget::factory()->archived()->create();

        $this->assertEquals('Active', $activeWidget->status_label);
        $this->assertEquals('Inactive', $inactiveWidget->status_label);
        $this->assertEquals('Archived', $archivedWidget->status_label);
    }

    public function test_formatted_price_accessor_returns_formatted_string()
    {
        $widget = Widget::factory()->create(['price' => 29.99]);

        $this->assertEquals('29.99', $widget->formatted_price);
    }

    public function test_formatted_price_accessor_returns_null_for_null_price()
    {
        $widget = Widget::factory()->create(['price' => null]);

        $this->assertNull($widget->formatted_price);
    }
}

