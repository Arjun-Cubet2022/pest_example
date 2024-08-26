<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_a_list_of_all_products()
    {
        // Arrange
        Product::factory()->count(5)->create();

        // Act
        $response = $this->getJson('/api/products');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    /** @test */
    public function it_returns_a_single_product_by_id()
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $response = $this->getJson("/api/products/{$product->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $product->id,
                'name' => $product->name,
            ]);
    }

    /** @test */
    public function it_can_store_a_new_product()
    {
        // Arrange: Prepare the data
        $data = [
            'name' => 'New Product',
            'description' => 'This is a test product',
            'price' => 199.99,
        ];

        // Act: Make a POST request to the store route
        $response = $this->postJson('/api/products', $data);

        // Assert: Check the response status and structure
        $response->assertStatus(201); // Assuming 201 Created
        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'description' => 'This is a test product',
            'price' => 199.99, // Ensure price is correctly formatted
        ]);
    }


    /** @test */
    public function it_updates_an_existing_product()
    {
        // Arrange
        $product = Product::factory()->create();

        $updatedData = [
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'price' => "150.00",
        ];

        // Act
        $response = $this->putJson("/api/products/{$product->id}", $updatedData);

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment($updatedData);

        $this->assertDatabaseHas('products', $updatedData);
    }

     /** @test */
    public function it_deletes_a_product()
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $response = $this->deleteJson("/api/products/{$product->id}");

        // Assert
        $response->assertStatus(204);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }


    /** @test */
    public function it_returns_validation_errors_when_required_fields_are_missing()
    {
        // Arrange: Prepare invalid data (missing 'name', 'description', and 'price')
        $invalidData = [];

        // Act: Send a POST request to the /products endpoint
        $response = $this->postJson('/api/products', $invalidData);

        // Assert: Check for validation error
        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonValidationErrors(['name', 'price']);
    }

    /** @test */
    public function it_returns_validation_errors_when_price_is_invalid()
    {
        // Arrange: Prepare data with an invalid 'price'
        $invalidData = [
            'name' => 'Test Product',
            'description' => 'This is a test description.',
            'price' => 'invalid-price', // Invalid price (should be numeric)
        ];

        // Act: Send a POST request to the /products endpoint
        $response = $this->postJson('/api/products', $invalidData);

        // Assert: Check for validation errors
        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonValidationErrors(['price']);
    }
}
