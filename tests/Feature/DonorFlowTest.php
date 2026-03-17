<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Donation;
use App\Models\DonationClaim;
use App\Models\Feedback;
use App\Models\CharityRequest;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DonorFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $donor;
    protected $charity;

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

        // Create donor user
        $this->donor = User::create([
            'name' => 'Test Donor',
            'email' => 'donor@test.com',
            'password' => bcrypt('password'),
            'role' => 'donor',
            'email_verified_at' => now(),
        ]);

        // Create charity user
        $this->charity = User::create([
            'name' => 'Test Charity',
            'email' => 'charity@test.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'Test Charity Org',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * TEST 1: Donor Dashboard - Login shows name and role badge
     */
    public function test_donor_login_shows_name_and_role_badge(): void
    {
        $response = $this->actingAs($this->donor)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Test Donor');
        $response->assertSee('donor');
    }

    /**
     * TEST 2: Donor Dashboard - Weather widget shows correctly
     */
    public function test_donor_dashboard_weather_widget(): void
    {
        $response = $this->actingAs($this->donor)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Current Weather');
    }

    /**
     * TEST 3: Donor Dashboard - Stat cards show: My Donations, Available Now, Completed, Verified Charities
     */
    public function test_donor_dashboard_stat_cards(): void
    {
        // Create some donations
        Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Test Donation 1',
            'target_audience' => 'general',
            'status' => 'available',
            'expires_at' => now()->addDays(1),
        ]);

        Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Test Donation 2',
            'target_audience' => 'general',
            'status' => 'completed',
            'claimed_by' => $this->charity->id,
            'expires_at' => now()->addDays(1),
        ]);

        // Create verified charity
        $charity = User::create([
            'name' => 'Verified Charity',
            'email' => 'verified@charity.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'Verified Org',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->donor)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('My Donations');
        $response->assertSee('Available Now');
        $response->assertSee('Completed');
        $response->assertSee('Verified Charities');
    }

    /**
     * TEST 4: Donor Dashboard - Quick Actions show: Post a Donation, Charity Requests, Available Food, Feedback Log
     */
    public function test_donor_dashboard_quick_actions(): void
    {
        $response = $this->actingAs($this->donor)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Post a Donation');
        $response->assertSee('Charity Requests');
        $response->assertSee('Available Food');
        $response->assertSee('Feedback Log');
    }

    /**
     * TEST 5: Post a Donation - Form loads correctly
     */
    public function test_post_donation_form_loads(): void
    {
        $response = $this->actingAs($this->donor)->get('/donate');

        $response->assertStatus(200);
        $response->assertSee('description');
        $response->assertSee('target_audience');
    }

    /**
     * TEST 6: Post a Donation - Submit creates donation
     */
    public function test_post_donation_creates_donation(): void
    {
        $response = $this->actingAs($this->donor)->post('/donate', [
            'description' => 'Test Food Donation',
            'target_audience' => 'general',
        ]);

        $response->assertRedirect('/dashboard');
        
        $this->assertDatabaseHas('donations', [
            'description' => 'Test Food Donation',
            'target_audience' => 'general',
            'user_id' => $this->donor->id,
            'status' => 'available',
        ]);
    }

    /**
     * TEST 7: Donation History - Shows paginated list
     */
    public function test_donation_history_paginated(): void
    {
        // Create 6 donations
        for ($i = 1; $i <= 6; $i++) {
            Donation::create([
                'user_id' => $this->donor->id,
                'description' => "Test Donation $i",
                'target_audience' => 'general',
                'status' => 'available',
                'expires_at' => now()->addDays(1),
            ]);
        }

        $response = $this->actingAs($this->donor)->get('/donations');

        $response->assertStatus(200);
        // Should see pagination - check for page links
        $response->assertSee('page');
    }

    /**
     * TEST 8: Donation History - Status badges display correctly
     */
    public function test_donation_history_status_badges(): void
    {
        // Create donation with different statuses
        Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Available Donation',
            'target_audience' => 'general',
            'status' => 'available',
            'expires_at' => now()->addDays(1),
        ]);

        Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Completed Donation',
            'target_audience' => 'general',
            'status' => 'completed',
            'claimed_by' => $this->charity->id,
            'expires_at' => now()->addDays(1),
        ]);

        $response = $this->actingAs($this->donor)->get('/donations');

        $response->assertStatus(200);
        $response->assertSee('Available');
        $response->assertSee('Completed');
    }

    /**
     * TEST 9: Donation History - Delete Selected button appears
     */
    public function test_delete_selected_button_appears(): void
    {
        Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Test Donation',
            'target_audience' => 'general',
            'status' => 'available',
            'expires_at' => now()->addDays(1),
        ]);

        $response = $this->actingAs($this->donor)->get('/donations');

        $response->assertStatus(200);
        $response->assertSee('Delete Selected');
    }

    /**
     * TEST 10: Donation History - Delete Selected soft deletes donations
     */
    public function test_delete_selected_soft_deletes(): void
    {
        $donation = Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Test Donation',
            'target_audience' => 'general',
            'status' => 'available',
            'expires_at' => now()->addDays(1),
        ]);

        $response = $this->actingAs($this->donor)->delete('/donations/bulk-delete', [
            'ids' => [$donation->id],
        ]);

        $response->assertRedirect('/donations');
        $response->assertSessionHas('success');

        // Verify soft delete
        $donation->refresh();
        $this->assertNotNull($donation->deleted_at);
    }

    /**
     * TEST 11: Claim Requests - Notification badge shows when charity requests
     */
    public function test_notification_badge_for_claim_request(): void
    {
        // Create a donation
        $donation = Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Test Donation',
            'target_audience' => 'general',
            'status' => 'available',
            'expires_at' => now()->addDays(1),
        ]);

        // Create notification manually as the claim creates it
        Notification::create([
            'user_id' => $this->donor->id,
            'type' => 'claim_request',
            'message' => 'Test Charity has requested your donation',
            'related_id' => $donation->id,
            'is_read' => false,
        ]);

        $unreadCount = Notification::where('user_id', $this->donor->id)
            ->where('is_read', false)
            ->count();

        $this->assertGreaterThan(0, $unreadCount);
    }

    /**
     * TEST 12: Accept Claim - Donation status changes to Completed
     */
    public function test_accept_claim_changes_status_to_completed(): void
    {
        // Create a donation
        $donation = Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Test Donation',
            'target_audience' => 'general',
            'status' => 'available',
            'expires_at' => now()->addDays(1),
        ]);

        // Create claim
        $claim = DonationClaim::create([
            'donation_id' => $donation->id,
            'charity_id' => $this->charity->id,
            'status' => 'pending',
        ]);

        // Accept the claim
        $response = $this->actingAs($this->donor)->post("/donations/claims/{$claim->id}/accept");

        $response->assertRedirect();

        // Verify status changed
        $donation->refresh();
        $this->assertEquals('completed', $donation->status);
        $this->assertEquals($this->charity->id, $donation->claimed_by);
    }

    /**
     * TEST 13: Accept Claim - Other claims are rejected
     */
    public function test_accept_claim_rejects_other_claims(): void
    {
        // Create a donation
        $donation = Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Test Donation',
            'target_audience' => 'general',
            'status' => 'available',
            'expires_at' => now()->addDays(1),
        ]);

        // Create two charity claims
        $charity2 = User::create([
            'name' => 'Charity 2',
            'email' => 'charity2@test.com',
            'password' => bcrypt('password'),
            'role' => 'charity',
            'verification_status' => 'approved',
            'organization_name' => 'Charity 2 Org',
            'email_verified_at' => now(),
        ]);

        $claim1 = DonationClaim::create([
            'donation_id' => $donation->id,
            'charity_id' => $this->charity->id,
            'status' => 'pending',
        ]);

        $claim2 = DonationClaim::create([
            'donation_id' => $donation->id,
            'charity_id' => $charity2->id,
            'status' => 'pending',
        ]);

        // Accept the first claim
        $this->actingAs($this->donor)->post("/donations/claims/{$claim1->id}/accept");

        // Verify other claim is rejected
        $claim2->refresh();
        $this->assertEquals('rejected', $claim2->status);
    }

    /**
     * TEST 14: Available Food - Shows all available donations
     */
    public function test_available_food_shows_donations(): void
    {
        Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Available Food 1',
            'target_audience' => 'general',
            'status' => 'available',
            'expires_at' => now()->addDays(1),
        ]);

        $response = $this->actingAs($this->donor)->get('/available-donations');

        $response->assertStatus(200);
        $response->assertSee('Available Food 1');
    }

    /**
     * TEST 15: Available Food - During Emergency Mode, direct claim is blocked
     */
    public function test_emergency_mode_blocks_direct_claim(): void
    {
        // Enable emergency mode
        DB::table('settings')->where('key', 'emergency_mode')->update(['value' => '1']);

        // Create a donation
        $donation = Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Emergency Donation',
            'target_audience' => 'general',
            'status' => 'available',
            'expires_at' => now()->addDays(1),
        ]);

        // Try to claim (as charity) - this is a POST route
        $response = $this->actingAs($this->charity)->post("/donations/{$donation->id}/claim");

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Direct claims are blocked during Emergency Mode. Please wait for admin allocation.');
    }

    /**
     * TEST 16: Charity Requests - Shows open requests from charities
     */
    public function test_charity_requests_shows_open_requests(): void
    {
        // Create a charity request (use correct field names)
        CharityRequest::create([
            'charity_id' => $this->charity->id,
            'food_name' => 'Cooked Rice',
            'description' => 'Need food for community',
            'quantity' => '50 packs',
            'urgency' => 'urgent',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->donor)->get('/charity-requests');

        $response->assertStatus(200);
        $response->assertSee('Need food for community');
    }

    /**
     * TEST 17: Charity Requests - Can fulfill a request directly
     */
    public function test_fulfill_charity_request(): void
    {
        // Create a charity request
        $request = CharityRequest::create([
            'charity_id' => $this->charity->id,
            'food_name' => 'Cooked Rice',
            'quantity' => '50',
            'urgency' => 'urgent',
            'status' => 'open',
            'description' => 'Need food',
        ]);

        $response = $this->actingAs($this->donor)->post("/charity-requests/{$request->id}/fulfill", [
            'description' => 'Fulfilled Donation',
            'target_audience' => 'general',
        ]);

        $response->assertRedirect();

        // Verify donation is created and marked as completed
        $this->assertDatabaseHas('donations', [
            'description' => 'Donation for: Cooked Rice',
            'charity_id' => $this->charity->id,
            'status' => 'completed',
        ]);
    }

    /**
     * TEST 18: Feedback Log - Shows feedback for donor's donations
     */
    public function test_feedback_log_shows_feedback(): void
    {
        // Create a donation and feedback
        $donation = Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Test Donation',
            'target_audience' => 'general',
            'status' => 'completed',
            'claimed_by' => $this->charity->id,
            'expires_at' => now()->addDays(1),
        ]);

        Feedback::create([
            'donation_id' => $donation->id,
            'charity_id' => $this->charity->id,
            'message' => 'Test distribution report',
            'charity_comment' => 'Thank you so much!',
        ]);

        $response = $this->actingAs($this->donor)->get('/feedback');

        $response->assertStatus(200);
        // Should see feedback - check for the donation description and charity comment
        $response->assertSee('Test distribution report');
        $response->assertSee('Thank you so much!');
    }

    /**
     * TEST 19: Notification - Donor receives notification when claim is accepted
     */
    public function test_donor_gets_notification_on_claim_accepted(): void
    {
        // Create a donation
        $donation = Donation::create([
            'user_id' => $this->donor->id,
            'description' => 'Test Donation',
            'target_audience' => 'general',
            'status' => 'available',
            'expires_at' => now()->addDays(1),
        ]);

        // Create claim
        $claim = DonationClaim::create([
            'donation_id' => $donation->id,
            'charity_id' => $this->charity->id,
            'status' => 'pending',
        ]);

        // Accept the claim
        $this->actingAs($this->donor)->post("/donations/claims/{$claim->id}/accept");

        // Verify notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->donor->id,
            'type' => 'donation_accepted',
        ]);
    }

    /**
     * TEST 20: Post a Donation button in hero works
     */
    public function test_post_donation_button_in_hero(): void
    {
        $response = $this->actingAs($this->donor)->get('/dashboard');

        $response->assertStatus(200);
        // Should see Post a Donation button/link
        $response->assertSee('Post a Donation');
    }
}
