<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
        $this->category = Category::factory()->create();
    }

    /**
     * Test that anyone can view products list.
     */
    public function test_anyone_can_view_products_list(): void
    {
        Product::factory()->count(5)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'price', 'category_id', 'image', 'category']
                ],
                'current_page',
                'last_page',
                'per_page',
                'total',
            ]);
    }

    /**
     * Test that anyone can view a single product.
     */
    public function test_anyone_can_view_single_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'description', 'price', 'category_id', 'image', 'category']);
    }

    /**
     * Test products can be filtered by category.
     */
    public function test_products_can_be_filtered_by_category(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Product::factory()->count(3)->create(['category_id' => $category1->id]);
        Product::factory()->count(2)->create(['category_id' => $category2->id]);

        $response = $this->getJson("/api/products?category={$category1->id}");

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test products can be filtered by price range.
     */
    public function test_products_can_be_filtered_by_price_range(): void
    {
        Product::factory()->create(['category_id' => $this->category->id, 'price' => 50]);
        Product::factory()->create(['category_id' => $this->category->id, 'price' => 100]);
        Product::factory()->create(['category_id' => $this->category->id, 'price' => 150]);

        $response = $this->getJson('/api/products?min_price=75&max_price=125');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * Test products can be searched by name.
     */
    public function test_products_can_be_searched_by_name(): void
    {
        Product::factory()->create(['category_id' => $this->category->id, 'name' => 'iPhone 15']);
        Product::factory()->create(['category_id' => $this->category->id, 'name' => 'Samsung Galaxy']);

        $response = $this->getJson('/api/products?search=iPhone');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * Test admin can create a product.
     */
    public function test_admin_can_create_product(): void
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'product']);

        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    /**
     * Test admin can create product with image.
     */
    public function test_admin_can_create_product_with_image(): void
    {
        Storage::fake('public');

        $productData = [
            'name' => 'Product With Image',
            'description' => 'Has an image',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'image' => UploadedFile::fake()->image('product.jpg'),
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/products', $productData);

        $response->assertStatus(201);

        $product = Product::where('name', 'Product With Image')->first();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    /**
     * Test regular user cannot create a product.
     */
    public function test_regular_user_cannot_create_product(): void
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->regularUser)
            ->postJson('/api/products', $productData);

        $response->assertStatus(403);
    }

    /**
     * Test admin can update a product.
     */
    public function test_admin_can_update_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $updateData = [
            'name' => 'Updated Product Name',
            'price' => 199.99,
        ];

        $response = $this->actingAs($this->adminUser)
            ->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('product.name', 'Updated Product Name')
            ->assertJsonPath('product.price', '199.99');
    }

    /**
     * Test regular user cannot update a product.
     */
    public function test_regular_user_cannot_update_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->regularUser)
            ->putJson("/api/products/{$product->id}", ['name' => 'Updated']);

        $response->assertStatus(403);
    }

    /**
     * Test admin can delete a product.
     */
    public function test_admin_can_delete_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Product deleted successfully']);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /**
     * Test regular user cannot delete a product.
     */
    public function test_regular_user_cannot_delete_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->regularUser)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(403);
    }

    /**
     * Test product validation on create.
     */
    public function test_product_validation_on_create(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'category_id']);
    }

    /**
     * Test product requires valid category.
     */
    public function test_product_requires_valid_category(): void
    {
        $productData = [
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => 9999,
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/products', $productData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }
}
