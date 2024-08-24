<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Models\Product;


class ProductControllerTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ProductController();
    }

    /** @test */
    public function it_stores_function_works()
    {
        // Arrange: Prepare a mock request
        $request = Request::create('/products', 'POST', [
            'name' => 'New Product',
            'description' => 'Product description',
            'price' => 199.99,
            'quantity' => 10,
        ]);

        // Act: Call the store method
        $response = $this->controller->store($request);

        // Assert: Verify the product is saved in the database
        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'description' => 'Product description',
            'price' => 199.99,
        ]);
    }

    /** @test */
    public function it_updates_function_works()
    {
        // Arrange: Create a product and prepare a mock request
        $product = Product::factory()->create();
        $request = Request::create('/products/' . $product->id, 'PUT', [
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'price' => 249.99,
        ]);

        // Act: Call the update method
        $response = $this->controller->update($request, $product->id);

        // Assert: Verify the product is updated in the database
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'price' => 249.99,
        ]);
    }

    /** @test */
    public function it_transforms_product_data_correctly()
    {
        $data = [
            'name' => 'test product',
            'description' => 'Description here',
            'price' => 49.99,
        ];

        $transformedData = $this->controller->transformProductData($data);

        $this->assertEquals('Test product', $transformedData['name']);
    }
}
