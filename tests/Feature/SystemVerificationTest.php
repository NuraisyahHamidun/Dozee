<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Manager;
use App\Models\Salesmen;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SystemVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected $manager;
    protected $salesmen;
    protected $category;
    protected $products = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the detergent database seeder
        $this->seed(\Database\Seeders\DetergentProductSeeder::class);

        // Retrieve seeded entities
        $this->manager = Manager::where('email', 'nuraisyahsiti793@gmail.com')->first();
        $this->salesmen = Salesmen::where('email', 'ariff@dozee.com')->first();
        $this->category = Category::where('name', 'Detergent')->first();
        $this->products = Product::all();
    }

    /** @test */
    public function test_exactly_30_detergent_products_exist()
    {
        $this->assertEquals(30, Product::where('category', 'Detergent')->count());
    }

    /** @test */
    public function test_manager_login_and_dashboard_access()
    {
        $response = $this->post('/manager/login', [
            'email' => 'nuraisyahsiti793@gmail.com',
            'password' => 'Nurisy@22',
        ]);

        $response->assertRedirect(route('dashboard'));

        $dashboardResponse = $this->actingAs($this->manager, 'manager')
            ->get(route('dashboard'));

        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Nur Aisyah Siti');
    }

    /** @test */
    public function test_salesmen_login_and_dashboard_access()
    {
        $response = $this->post('/salesmen/login', [
            'email' => 'ariff@dozee.com',
            'password' => 'Ariff@1234',
        ]);

        $response->assertRedirect(route('dashboard'));

        $dashboardResponse = $this->actingAs($this->salesmen, 'salesmen')
            ->get(route('dashboard'));

        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Nur Ariff');
    }

    /** @test */
    public function test_manager_product_crud()
    {
        $this->actingAs($this->manager, 'manager');

        // Create product
        $response = $this->post(route('products.store'), [
            'item_name' => 'New Test Dozee Detergent',
            'volume' => '2L',
            'category_id' => $this->category->id,
            'description' => 'High quality test detergent',
            'price' => 35.50,
            'stock_qty' => 80,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('item', ['item_name' => 'New Test Dozee Detergent']);

        $product = Product::where('item_name', 'New Test Dozee Detergent')->first();

        // Update product
        $updateResponse = $this->patch(route('products.update', $product->item_id), [
            'item_name' => 'Updated Test Dozee Detergent',
            'volume' => '2.5L',
            'category_id' => $this->category->id,
            'description' => 'Updated high quality test detergent',
            'price' => 40.00,
            'stock_qty' => 90,
        ]);

        $updateResponse->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('item', ['item_name' => 'Updated Test Dozee Detergent']);

        // Delete product
        $deleteResponse = $this->delete(route('products.destroy', $product->item_id));
        $deleteResponse->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('item', ['item_name' => 'Updated Test Dozee Detergent']);
    }

    /** @test */
    public function test_salesmen_read_only_items_access_and_search()
    {
        $this->actingAs($this->salesmen, 'salesmen');

        // View items list
        $response = $this->get(route('salesmen.items.index'));
        $response->assertStatus(200);
        $response->assertSee('Dozee Ultra Liquid 1L');

        // Search items
        $searchResponse = $this->get(route('salesmen.items.index', ['search' => 'Powder']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Dozee Power Powder 2kg');
        $searchResponse->assertDontSee('Dozee Ultra Liquid 1L');

        // Verify salesmen cannot access manager products CRUD routes (gets redirected due to middleware)
        $createResponse = $this->get(route('products.create'));
        $createResponse->assertRedirect();

        $storeResponse = $this->post(route('products.store'), [
            'item_name' => 'Invalid Creation',
            'volume' => '1L',
            'category_id' => $this->category->id,
            'price' => 10,
            'stock_qty' => 10,
        ]);
        $storeResponse->assertRedirect();
    }

    /** @test */
    public function test_sales_and_reports_generation()
    {
        // Get initial stock before the sale
        $initialStock0 = $this->products[0]->fresh()->stock_qty;
        $initialStock1 = $this->products[1]->fresh()->stock_qty;

        // 1. Salesmen records a sale
        $this->actingAs($this->salesmen, 'salesmen');

        $response = $this->post(route('sales.store'), [
            'sale_date' => now()->setHour(12)->setMinute(0)->format('Y-m-d H:i:s'),
            'event_name' => 'Roadshow Test',
            'items' => [
                [
                    'product_id' => $this->products[0]->item_id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $this->products[1]->item_id,
                    'quantity' => 1,
                ]
            ]
        ]);

        $response->assertRedirect(route('sales.index'));

        // Verify sale is created in DB with status Pending and total amount is calculated
        $expectedTotal = ($this->products[0]->price * 2) + ($this->products[1]->price * 1);
        $this->assertDatabaseHas('sales_transaction', [
            'salesmen_id' => $this->salesmen->salesmen_id,
            'event_name' => 'Roadshow Test',
            'status' => 'Pending',
            'total_amount' => $expectedTotal,
        ]);

        $sale = Sale::where('event_name', 'Roadshow Test')->first();

        // 2. Manager approves the sale
        $this->actingAs($this->manager, 'manager');
        $approveResponse = $this->post(route('sales.approve', $sale->transaction_id));
        $approveResponse->assertRedirect();

        $this->assertEquals('Approved', $sale->fresh()->status);

        // Verify stock is decremented
        $this->assertEquals($initialStock0 - 2, $this->products[0]->fresh()->stock_qty);
        $this->assertEquals($initialStock1 - 1, $this->products[1]->fresh()->stock_qty);

        // 3. Manager accesses the report page
        $reportResponse = $this->get(route('reports.index', [
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDay()->format('Y-m-d'),
        ]));
        
        $reportResponse->assertStatus(200);
        $reportResponse->assertSee('Nur Ariff');
    }

    /** @test */
    public function test_product_auto_generates_sequential_item_code()
    {
        $this->actingAs($this->manager, 'manager');

        $response = $this->post(route('products.store'), [
            'item_name' => 'Auto Code Detergent',
            'volume' => '1L',
            'category_id' => $this->category->id,
            'price' => 12.50,
            'stock_qty' => 100,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('item', ['item_name' => 'Auto Code Detergent']);

        $product = Product::where('item_name', 'Auto Code Detergent')->first();
        $this->assertMatchesRegularExpression('/^ITM-\d{4}$/', $product->item_code);
    }

    /** @test */
    public function test_product_manual_override_validation()
    {
        $this->actingAs($this->manager, 'manager');

        // 1. Test invalid format (doesn't start with ITM-)
        $response = $this->post(route('products.store'), [
            'item_name' => 'Invalid Override Detergent',
            'volume' => '1L',
            'category_id' => $this->category->id,
            'price' => 12.50,
            'stock_qty' => 100,
            'override_item_code' => '1',
            'item_code' => 'INVALID-1234',
        ]);
        $response->assertSessionHasErrors('item_code');

        // 2. Test valid manual override format
        $response2 = $this->post(route('products.store'), [
            'item_name' => 'Valid Override Detergent',
            'volume' => '1L',
            'category_id' => $this->category->id,
            'price' => 12.50,
            'stock_qty' => 100,
            'override_item_code' => '1',
            'item_code' => 'ITM-OVERRIDE-1',
        ]);
        $response2->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('item', ['item_code' => 'ITM-OVERRIDE-1']);

        // 3. Test duplicate override code
        $response3 = $this->post(route('products.store'), [
            'item_name' => 'Duplicate Override Detergent',
            'volume' => '1L',
            'category_id' => $this->category->id,
            'price' => 12.50,
            'stock_qty' => 100,
            'override_item_code' => '1',
            'item_code' => 'ITM-OVERRIDE-1',
        ]);
        $response3->assertSessionHasErrors('item_code');
    }

    /** @test */
    public function test_apriori_report_uses_item_code()
    {
        $itemA = Product::first();
        $itemB = Product::skip(1)->first();

        $itemA->update(['item_code' => 'ITM-MOCK-A']);
        $itemB->update(['item_code' => 'ITM-MOCK-B']);

        \App\Models\AprioriAnalysis::create([
            'antecedent' => (string) $itemA->item_id,
            'consequent' => (string) $itemB->item_id,
            'support' => 0.25,
            'confidence' => 0.75,
            'lift' => 1.5,
        ]);

        $this->actingAs($this->manager, 'manager');
        $response = $this->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('ITM-MOCK-A');
        $response->assertSee('ITM-MOCK-B');
    }

    /** @test */
    public function test_market_basket_analysis_page_loads()
    {
        \App\Models\AprioriAnalysis::query()->delete();

        $itemA = Product::first();
        $itemB = Product::skip(1)->first();

        \App\Models\AprioriAnalysis::create([
            'antecedent' => (string) $itemA->item_id,
            'consequent' => (string) $itemB->item_id,
            'support' => 0.25,
            'confidence' => 0.75,
            'lift' => 1.5,
            'rule_text' => $itemA->item_name . ' ==> ' . $itemB->item_name,
        ]);

        $this->actingAs($this->manager, 'manager');
        $response = $this->get(route('analysis.index'));
        $response->assertStatus(200);
        $response->assertSee('Market Basket Analysis + Association Insight Dashboard');
        $response->assertSee($itemA->item_name);
        $response->assertSee($itemB->item_name);
    }

    /** @test */
    public function test_create_promotion_prefills_correctly_from_rule_id()
    {
        $itemA = Product::first();
        $itemB = Product::skip(1)->first();

        $rule = \App\Models\AprioriAnalysis::create([
            'antecedent' => (string) $itemA->item_id,
            'consequent' => (string) $itemB->item_id,
            'support' => 0.25,
            'confidence' => 0.75,
            'lift' => 1.5,
            'rule_text' => $itemA->item_name . ' ==> ' . $itemB->item_name,
        ]);

        $this->actingAs($this->manager, 'manager');
        $response = $this->get(route('promotions.create', ['rule_id' => $rule->rule_id]));
        $response->assertStatus(200);
        $response->assertSee('Combo: ' . $itemA->item_name . ' & ' . $itemB->item_name);
        $response->assertSee(round($rule->support * 100, 2) . '%');
        $response->assertSee(round($rule->confidence * 100, 1) . '%');
        $response->assertSee(round($rule->lift, 2));
    }
}
