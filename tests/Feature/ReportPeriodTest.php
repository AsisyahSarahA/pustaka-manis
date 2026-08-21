<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use App\Models\Member;
use App\Models\Loan;
use App\Models\VisitorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportPeriodTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::first() ?? User::factory()->create();
    }

    public function test_circulation_report_weekly_monthly_yearly_filters(): void
    {
        $this->actingAs($this->user);

        // Test Monthly (Default)
        $response = $this->get(route('reports.index', ['type' => 'monthly', 'period_type' => 'monthly', 'month' => 8, 'year' => 2026]));
        $response->assertStatus(200);
        $response->assertSee('Sirkulasi Ringkasan');

        // Test Weekly
        $response = $this->get(route('reports.index', ['type' => 'monthly', 'period_type' => 'weekly', 'week' => 2, 'month' => 8, 'year' => 2026]));
        $response->assertStatus(200);
        $response->assertSee('Minggu ke-2');

        // Test Yearly
        $response = $this->get(route('reports.index', ['type' => 'monthly', 'period_type' => 'yearly', 'year' => 2026]));
        $response->assertStatus(200);
        $response->assertSee('Tahun 2026');

        // Test Custom
        $response = $this->get(route('reports.index', ['type' => 'monthly', 'period_type' => 'custom', 'start' => '2026-08-01', 'end' => '2026-08-15']));
        $response->assertStatus(200);
    }

    public function test_loans_and_visitors_period_filters(): void
    {
        $this->actingAs($this->user);

        // Loans weekly
        $response = $this->get(route('reports.index', ['type' => 'loans', 'period_type' => 'weekly', 'week' => 1, 'month' => 8, 'year' => 2026]));
        $response->assertStatus(200);

        // Visitors yearly
        $response = $this->get(route('reports.index', ['type' => 'visitors', 'period_type' => 'yearly', 'year' => 2026]));
        $response->assertStatus(200);

        // Overdue custom
        $response = $this->get(route('reports.index', ['type' => 'overdue', 'period_type' => 'custom', 'start' => '2026-01-01', 'end' => '2026-12-31']));
        $response->assertStatus(200);

        // Inventory
        $response = $this->get(route('reports.index', ['type' => 'inventory']));
        $response->assertStatus(200);
    }

    public function test_export_pdf_and_excel_and_word(): void
    {
        $this->actingAs($this->user);

        // PDF Export
        $response = $this->get(route('reports.export', ['type' => 'monthly', 'period_type' => 'monthly', 'month' => 8, 'year' => 2026, 'format' => 'pdf']));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));

        // Excel Export
        $response = $this->get(route('reports.export', ['type' => 'monthly', 'period_type' => 'yearly', 'year' => 2026, 'format' => 'excel']));
        $response->assertStatus(200);

        // Word Export
        $response = $this->get(route('reports.export', ['type' => 'monthly', 'period_type' => 'weekly', 'week' => 3, 'month' => 8, 'year' => 2026, 'format' => 'word']));
        $response->assertStatus(200);
    }
}
