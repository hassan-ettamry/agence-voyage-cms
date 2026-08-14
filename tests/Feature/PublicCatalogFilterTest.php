<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Destination;
use App\Models\Offer;
use App\Support\AgencyContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        AgencyContext::clear();
        parent::tearDown();
    }

    public function test_destination_search_and_filters_only_return_published_content_for_the_active_agency(): void
    {
        [$atlas, $ocean] = [$this->agency('Atlas'), $this->agency('Ocean')];
        $this->destination($atlas, 'Marrakech Atlas', 'marrakech', 'Morocco', true);
        $this->destination($atlas, 'Lisbon Atlas', 'lisbon', 'Portugal');
        $this->destination($atlas, 'Draft Atlas', 'draft-city', 'Morocco', false, Destination::STATUS_DRAFT);
        $this->destination($ocean, 'Marrakech Ocean', 'marrakech', 'Morocco', true);

        $this->get(route('public.site.destinations.index', $atlas->slug).'?q=Marrakech')
            ->assertOk()->assertSee('Marrakech Atlas')->assertDontSee('Marrakech Ocean')->assertDontSee('Lisbon Atlas');
        $this->get(route('public.site.destinations.index', $atlas->slug).'?country=Portugal')
            ->assertOk()->assertSee('Lisbon Atlas')->assertDontSee('Marrakech Atlas');
        $this->get(route('public.site.destinations.index', $atlas->slug).'?featured=1')
            ->assertOk()->assertSee('Marrakech Atlas')->assertDontSee('Lisbon Atlas')->assertDontSee('Draft Atlas');
        $this->get(route('public.site.destinations.index', $atlas->slug).'?q=does-not-exist')
            ->assertOk()->assertSee('No destinations found');
    }

    public function test_offer_filters_are_tenant_aware_and_support_destination_budget_duration_and_special_status(): void
    {
        [$atlas, $ocean] = [$this->agency('Atlas'), $this->agency('Ocean')];
        $marrakech = $this->destination($atlas, 'Marrakech Atlas', 'marrakech', 'Morocco');
        $lisbon = $this->destination($atlas, 'Lisbon Atlas', 'lisbon', 'Portugal');
        $oceanDestination = $this->destination($ocean, 'Marrakech Ocean', 'marrakech', 'Morocco');
        $this->offer($atlas, $marrakech, 'Desert Signature', 'desert-signature', 1200, 7, true);
        $this->offer($atlas, $lisbon, 'Coastal Weekend', 'coastal-weekend', 500, 3);
        $this->offer($atlas, $marrakech, 'Draft Journey', 'draft-journey', 100, 2, true, Offer::STATUS_DRAFT);
        $this->offer($ocean, $oceanDestination, 'Ocean Secret', 'ocean-secret', 900, 5, true);

        $url = route('public.site.offers.index', $atlas->slug).'?destination=marrakech&min_price=1000&max_price=1500&duration=7&special=1';
        $this->get($url)->assertOk()
            ->assertSee('Desert Signature')
            ->assertDontSee('Coastal Weekend')
            ->assertDontSee('Draft Journey')
            ->assertDontSee('Ocean Secret');
        $this->get(route('public.site.offers.index', $atlas->slug).'?q=coastal')
            ->assertOk()->assertSee('Coastal Weekend')->assertDontSee('Desert Signature');
        $this->get(route('public.site.offers.index', $atlas->slug).'?max_price=10')
            ->assertOk()->assertSee('No journeys match these filters');
    }

    private function agency(string $name): Agency
    {
        return Agency::create(['name' => $name, 'slug' => strtolower($name).'-'.uniqid(), 'email' => fake()->unique()->safeEmail(), 'status' => 'active']);
    }

    private function destination(Agency $agency, string $name, string $slug, string $country, bool $featured = false, string $status = Destination::STATUS_PUBLISHED): Destination
    {
        return Destination::withoutGlobalScopes()->create(['agency_id' => $agency->id, 'name' => $name, 'slug' => $slug, 'country' => $country, 'description' => $name, 'is_featured' => $featured, 'status' => $status]);
    }

    private function offer(Agency $agency, Destination $destination, string $title, string $slug, float $price, int $days, bool $special = false, string $status = Offer::STATUS_PUBLISHED): Offer
    {
        return Offer::withoutGlobalScopes()->create(['agency_id' => $agency->id, 'destination_id' => $destination->id, 'title' => $title, 'slug' => $slug, 'description' => $title, 'price' => $price, 'duration_days' => $days, 'is_special' => $special, 'status' => $status]);
    }
}
