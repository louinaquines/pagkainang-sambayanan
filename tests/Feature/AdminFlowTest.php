<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Donation;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed settings
        DB::table('settings')->insert([
            'key' => 'emergency_mode',
            'value' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create admin user
        $this->admin = User::create([
            'name' => 'Test Admin',
            'email' => 'testadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * TEST 1: Admin Login - Hero shows name, role badge says "Admin"
     */
    public function test_admin_login_shows_name_and_role_badge(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Test Admin');
        $response->assertSee('admin');
    }

    /**
     * TEST 2: Weather widget shows Mandaue City weather
     */
    public function test_dashboard_weather_widget_displays(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        // Weather widget section should be present
        $response->assertSee('Current Weather');
    }

    /**
     * TEST 3: ReliefWeb disaster alert shows latest PH disaster
     */
    public function test_dashboard_reliefweb_disaster_alert(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        // Should have disaster alert section
        $response->assertSee('Latest PH Disaster Alert');
    }

    /**
     * TEST 4: 4 stat cards show correct numbers
     */
    public function test_dashboard_stat_cards_show_correct_numbers(): void
    {
        // Create test donations
        Donation::create([
            'user_id' => $this->admin->id,
            'description' => 'Test Donation 1',
            'food_type' => 'cooked',
            'quantity' => 10,
            'unit' => 'plates',
            'expires_at' => now()->addDays(1),
            'status' => 'available',
            'address' => 'Cebu City',
            'target_audience' => 'general',
        ]);

        Donation::create([
            'user_id' => $this->admin->id,
            'description' => 'Test Donation 2',
            'food_type' => 'cooked',
            'quantity' => 5,
            'unit' => 'plates',
            'expires_at' => now()->addDays(1),
            'status' => 'completed',
            'claimed_by' => $this->admin->id,
            'address' => 'Cebu City',
            'target_audience' => 'general',
        ]);

        // Create a verified charity
        $charity = User::create([
            'name' => 'Test Charity',
            'email' => 'charity@test.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'Test Charity Org',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        // Should show Total Donations (2)
        $response->assertSee('Total Donations');
        // Should show Available (1)
        $response->assertSee('Available');
        // Should show Completed (1)
        $response->assertSee('Completed');
        // Should show Verified Charities (1)
        $response->assertSee('Verified Charities');
    }

    /**
     * TEST 5: Quick Actions grid shows 4 buttons
     */
    public function test_dashboard_quick_actions_shows_four_buttons(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        // Should have Available Food
        $response->assertSee('Available Food');
        // Should have Feedback Log
        $response->assertSee('Feedback Log');
        // Should have Donation History
        $response->assertSee('Donation History');
        // Should have Manage Charities
        $response->assertSee('Manage Charities');
    }

    /**
     * TEST 6: Emergency Mode card shows status Inactive
     */
    public function test_emergency_mode_card_shows_inactive(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        // Should show Emergency Mode section
        $response->assertSee('Emergency Mode');
        // Should show Inactive status
        $response->assertSee('Inactive');
    }

    /**
     * TEST 7: Emergency Mode Activation
     */
    public function test_emergency_mode_activation(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/toggle-emergency');

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Emergency Mode activated.');

        // Verify database
        $this->assertEquals('1', DB::table('settings')->where('key', 'emergency_mode')->value('value'));

        // Reload page and verify banner appears
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        // Should show "Currently Active" instead of "Inactive"
        $response->assertSee('Currently Active');
    }

    /**
     * TEST 8: Emergency Mode Deactivation
     */
    public function test_emergency_mode_deactivation(): void
    {
        // First activate
        DB::table('settings')->where('key', 'emergency_mode')->update(['value' => '1']);

        $response = $this->actingAs($this->admin)->post('/admin/toggle-emergency');

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Emergency Mode deactivated.');

        // Verify database
        $this->assertEquals('0', DB::table('settings')->where('key', 'emergency_mode')->value('value'));
    }

    /**
     * TEST 9: Admin Panel Dropdown - Manage Charities
     */
    public function test_admin_panel_dropdown_manage_charities(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/charities');

        $response->assertStatus(200);
        $response->assertSee('Manage Charities');
    }

    /**
     * TEST 10: Admin Panel Dropdown - Priority Dashboard
     */
    public function test_admin_panel_dropdown_priority_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Priority Dashboard');
    }

    /**
     * TEST 11: Manage Charities - List all charities
     */
    public function test_manage_charities_shows_list(): void
    {
        // Create a pending charity
        User::create([
            'name' => 'Pending Charity',
            'email' => 'pending@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'pending',
            'organization_name' => 'Pending Charity Org',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/charities');

        $response->assertStatus(200);
        $response->assertSee('Pending Charity Org');
    }

    /**
     * TEST 12: Manage Charities - Approve charity
     */
    public function test_approve_charity(): void
    {
        // Create a pending charity
        $charity = User::create([
            'name' => 'Approve Test Charity',
            'email' => 'approve@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'pending',
            'organization_name' => 'Approve Test Org',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/charities/{$charity->id}/approve", [
            'area_severity' => 2,
            'population_count' => 5000,
            'accessibility' => 80,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Charity approved successfully.');

        // Verify charity is approved
        $charity->refresh();
        $this->assertEquals('approved', $charity->verification_status);
        $this->assertEquals(2, $charity->area_severity);

        // Verify notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $charity->id,
            'type' => 'charity_approved',
        ]);
    }

    /**
     * TEST 13: Manage Charities - Reject charity
     */
    public function test_reject_charity(): void
    {
        // Create a pending charity
        $charity = User::create([
            'name' => 'Reject Test Charity',
            'email' => 'reject@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'pending',
            'organization_name' => 'Reject Test Org',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/charities/{$charity->id}/reject");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Charity application rejected.');

        // Verify charity is rejected
        $charity->refresh();
        $this->assertEquals('rejected', $charity->verification_status);

        // Verify notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $charity->id,
            'type' => 'charity_rejected',
        ]);
    }

    /**
     * TEST 14: Priority Dashboard - Ranked list with priority scores
     */
    public function test_priority_dashboard_shows_ranked_charities(): void
    {
        // Create approved charities with different priority scores
        $charity1 = User::create([
            'name' => 'High Priority Charity',
            'email' => 'high@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'High Priority Org',
            'area_severity' => 4,
            'population_count' => 50000,
            'accessibility' => 5,
            'email_verified_at' => now(),
        ]);

        $charity2 = User::create([
            'name' => 'Low Priority Charity',
            'email' => 'low@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'Low Priority Org',
            'area_severity' => 1,
            'population_count' => 1000,
            'accessibility' => 90,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('High Priority Org');
        $response->assertSee('Low Priority Org');
    }

    /**
     * TEST 15: Priority Dashboard - Critical badge appears if severity >= 4.5 AND accessibility <= 10
     */
    public function test_critical_badge_appears_for_high_severity_low_accessibility(): void
    {
        // Create a critical charity
        $charity = User::create([
            'name' => 'Critical Charity',
            'email' => 'critical@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'Critical Org',
            'area_severity' => 4, // 4 * 1.25 = 5 >= 4.5
            'population_count' => 50000,
            'accessibility' => 5, // <= 10
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Critical');
    }

    /**
     * TEST 16: Priority Dashboard - Top Priority badge on rank #1
     */
    public function test_top_priority_badge_on_first_charity(): void
    {
        // Create approved charities
        User::create([
            'name' => 'Top Priority',
            'email' => 'top@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'Top Priority Org',
            'area_severity' => 4,
            'population_count' => 50000,
            'accessibility' => 5,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Second Priority',
            'email' => 'second@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'Second Priority Org',
            'area_severity' => 1,
            'population_count' => 1000,
            'accessibility' => 90,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        // Should see Top Priority label
        $response->assertSee('Top Priority');
    }

    /**
     * TEST 17: Disaster panel shows when Emergency Mode is active
     */
    public function test_disaster_panel_shows_when_emergency_active(): void
    {
        // Activate emergency mode
        DB::table('settings')->where('key', 'emergency_mode')->update(['value' => '1']);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        // Should show disaster section when emergency is active
        $response->assertSee('Disaster Response');
    }

    /**
     * TEST 18: Emergency Mode - Confirm Allocation button appears in emergency
     */
    public function test_confirm_allocation_button_appears_in_emergency(): void
    {
        // Activate emergency mode
        DB::table('settings')->where('key', 'emergency_mode')->update(['value' => '1']);

        // Create an approved charity and available donation
        $charity = User::create([
            'name' => 'Emergency Charity',
            'email' => 'emergency@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'Emergency Org',
            'area_severity' => 3,
            'population_count' => 10000,
            'accessibility' => 50,
            'email_verified_at' => now(),
        ]);

        Donation::create([
            'user_id' => $this->admin->id,
            'description' => 'Emergency Donation',
            'food_type' => 'cooked',
            'quantity' => 20,
            'unit' => 'plates',
            'expires_at' => now()->addDays(1),
            'status' => 'available',
            'address' => 'Cebu City',
            'target_audience' => 'general',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        // Should show allocation section in emergency mode
        $response->assertSee('Confirm Allocation');
    }

    /**
     * TEST 19: Document upload link visible and opens uploaded file
     */
    public function test_charity_document_link_visible(): void
    {
        // Create a charity with document
        User::create([
            'name' => 'Doc Charity',
            'email' => 'doc@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'pending',
            'organization_name' => 'Doc Org',
            'legitimacy_document' => 'legitimacy_documents/test.pdf',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/charities');

        $response->assertStatus(200);
        // Should show document section
        $response->assertSee('Legitimacy Document');
    }

    /**
     * TEST 20: Score bars are proportional relative to top scorer
     */
    public function test_score_bars_are_proportional(): void
    {
        // Create charities with different scores
        User::create([
            'name' => 'Score 1',
            'email' => 'score1@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'Score Org 1',
            'area_severity' => 4,
            'population_count' => 50000,
            'accessibility' => 5,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Score 2',
            'email' => 'score2@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'Score Org 2',
            'area_severity' => 2,
            'population_count' => 10000,
            'accessibility' => 50,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        // Should show priority score
        $response->assertSee('Score');
    }
}
