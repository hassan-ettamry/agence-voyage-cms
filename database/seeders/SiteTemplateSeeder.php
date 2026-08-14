<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteTemplate;
use App\Models\Theme;
use Illuminate\Database\Seeder;

class SiteTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $themes = Theme::query()
            ->whereIn('slug', [
                'ocean-blue',
                'sunset-luxe',
                'wild-nature',
                'urban-blue',
                'coastal-glow',
                'heritage-rose',
                'signature-travel',
            ])
            ->get()
            ->keyBy('slug');

        $templates = [
            [
                'name' => 'Signature Travel',
                'slug' => 'signature-travel',
                'description' => 'A confident, editorial travel website combining local expertise, premium journeys, and clear discovery paths.',
                'preview' => 'images/site-templates/sunset-luxe.png',
                'theme_id' => $themes->get('signature-travel')?->id,
                'pages' => $this->signatureTravelPages(),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Premium Escapes',
                'slug' => 'premium-escapes',
                'description' => 'A polished site template for premium tours, special offers, and custom travel requests.',
                'preview' => 'images/site-templates/sunset-luxe.png',
                'theme_id' => $themes->get('sunset-luxe')?->id,
                'pages' => $this->premiumEscapesPages('#f97316', '#111827'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Travel Agency Starter',
                'slug' => 'travel-agency-starter',
                'description' => 'A complete starter website for a travel agency: home, destinations, offers, and contact.',
                'preview' => 'images/site-templates/ocean-blue.png',
                'theme_id' => $themes->get('ocean-blue')?->id,
                'pages' => $this->travelStarterPages('#2563eb', '#0f172a'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Mountain Adventure',
                'slug' => 'mountain-adventure',
                'description' => 'Perfect template for adventure tours, hiking, and nature experiences.',
                'preview' => 'images/site-templates/mountain-adventure.png',
                'theme_id' => $themes->get('wild-nature')?->id,
                'pages' => $this->compactTravelPages('Adventure is calling', 'Guide hikers, climbers, and outdoor groups through curated trips.', '#2f7d55', '#102a2f', ['Home', 'Trips', 'About']),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'City Escape',
                'slug' => 'city-escape',
                'description' => 'City tours, galleries, events, and fast trip planning for modern travelers.',
                'preview' => 'images/site-templates/city-escape.png',
                'theme_id' => $themes->get('urban-blue')?->id,
                'pages' => $this->travelStarterPages('#0ea5e9', '#172554'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Beach Paradise',
                'slug' => 'beach-paradise',
                'description' => 'A bright package layout for beach stays, resort offers, and island tours.',
                'preview' => 'images/site-templates/beach-paradise.png',
                'theme_id' => $themes->get('coastal-glow')?->id,
                'pages' => $this->travelStarterPages('#0891b2', '#164e63'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Culture Journey',
                'slug' => 'culture-journey',
                'description' => 'An editorial template for heritage routes, local guides, and travel articles.',
                'preview' => 'images/site-templates/culture-journey.png',
                'theme_id' => $themes->get('heritage-rose')?->id,
                'pages' => $this->premiumEscapesPages('#be456b', '#431a2a'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Desert Retreat',
                'slug' => 'desert-retreat',
                'description' => 'A warm, focused draft for desert camps, private routes, and quiet luxury stays.',
                'preview' => 'images/site-templates/sunset-luxe.png',
                'theme_id' => $themes->get('sunset-luxe')?->id,
                'pages' => $this->compactTravelPages('Retreat into golden landscapes', 'Present private desert stays, guided routes, and calm seasonal escapes.', '#f97316', '#111827', ['Home', 'Retreats', 'About']),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Nordic Lights',
                'slug' => 'nordic-lights',
                'description' => 'A draft concept for winter breaks, northern lights packages, and scenic cruises.',
                'preview' => 'images/site-templates/ocean-blue.png',
                'theme_id' => $themes->get('ocean-blue')?->id,
                'pages' => $this->compactTravelPages('Chase the northern sky', 'Package winter views, cabins, cruises, and clear itinerary details.', '#2563eb', '#0f172a', ['Home', 'Packages', 'Guide']),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Safari Trails',
                'slug' => 'safari-trails',
                'description' => 'A draft tour layout for wildlife routes, lodge stays, and guided expeditions.',
                'preview' => 'images/site-templates/mountain-adventure.png',
                'theme_id' => $themes->get('wild-nature')?->id,
                'pages' => $this->compactTravelPages('Wild routes, expertly planned', 'Show lodges, guides, routes, and traveler expectations in one flow.', '#2f7d55', '#102a2f', ['Home', 'Routes', 'About']),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Wellness Retreat',
                'slug' => 'wellness-retreat',
                'description' => 'A draft for wellness stays, spa packages, yoga retreats, and calm itineraries.',
                'preview' => 'images/site-templates/beach-paradise.png',
                'theme_id' => $themes->get('coastal-glow')?->id,
                'pages' => $this->compactTravelPages('Reset beside the sea', 'Promote quiet resorts, recovery packages, and soft booking journeys.', '#0891b2', '#164e63', ['Home', 'Retreats', 'Contact']),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Island Hopping',
                'slug' => 'island-hopping',
                'description' => 'A ready-made structure for multi-island packages, ferries, beaches, and offers.',
                'preview' => 'images/site-templates/beach-paradise.png',
                'theme_id' => $themes->get('coastal-glow')?->id,
                'pages' => $this->travelStarterPages('#0891b2', '#164e63'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Luxury Packages',
                'slug' => 'luxury-packages',
                'description' => 'A polished sales layout for villas, concierge service, and premium travel offers.',
                'preview' => 'images/site-templates/sunset-luxe.png',
                'theme_id' => $themes->get('sunset-luxe')?->id,
                'pages' => $this->premiumEscapesPages('#f97316', '#111827'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Family Holidays',
                'slug' => 'family-holidays',
                'description' => 'A friendly template for family trips, kid-ready activities, and simple planning.',
                'preview' => 'images/site-templates/beach-paradise.png',
                'theme_id' => $themes->get('coastal-glow')?->id,
                'pages' => $this->travelStarterPages('#0891b2', '#164e63'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Food Tours',
                'slug' => 'food-tours',
                'description' => 'A local-experience template for culinary walks, markets, classes, and stories.',
                'preview' => 'images/site-templates/culture-journey.png',
                'theme_id' => $themes->get('heritage-rose')?->id,
                'pages' => $this->premiumEscapesPages('#be456b', '#431a2a'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Road Trip Planner',
                'slug' => 'road-trip-planner',
                'description' => 'A practical website structure for routes, stops, guides, and downloadable plans.',
                'preview' => 'images/site-templates/city-escape.png',
                'theme_id' => $themes->get('urban-blue')?->id,
                'pages' => $this->travelStarterPages('#0ea5e9', '#172554'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Cruise Explorer',
                'slug' => 'cruise-explorer',
                'description' => 'A clean ocean-focused template for itineraries, ships, decks, and port offers.',
                'preview' => 'images/site-templates/ocean-blue.png',
                'theme_id' => $themes->get('ocean-blue')?->id,
                'pages' => $this->travelStarterPages('#2563eb', '#0f172a'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Honeymoon Escape',
                'slug' => 'honeymoon-escape',
                'description' => 'A romantic package layout for resorts, private tours, and quote requests.',
                'preview' => 'images/site-templates/sunset-luxe.png',
                'theme_id' => $themes->get('sunset-luxe')?->id,
                'pages' => $this->premiumEscapesPages('#f97316', '#111827'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Weekend Breaks',
                'slug' => 'weekend-breaks',
                'description' => 'A fast-scanning template for short stays, nearby cities, and limited offers.',
                'preview' => 'images/site-templates/city-escape.png',
                'theme_id' => $themes->get('urban-blue')?->id,
                'pages' => $this->travelStarterPages('#0ea5e9', '#172554'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Eco Lodges',
                'slug' => 'eco-lodges',
                'description' => 'A nature-first template for sustainable stays, low-impact trips, and guides.',
                'preview' => 'images/site-templates/mountain-adventure.png',
                'theme_id' => $themes->get('wild-nature')?->id,
                'pages' => $this->travelStarterPages('#2f7d55', '#102a2f'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Pilgrimage Routes',
                'slug' => 'pilgrimage-routes',
                'description' => 'A calm heritage template for spiritual journeys, group routes, and assistance.',
                'preview' => 'images/site-templates/culture-journey.png',
                'theme_id' => $themes->get('heritage-rose')?->id,
                'pages' => $this->premiumEscapesPages('#be456b', '#431a2a'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Student Trips',
                'slug' => 'student-trips',
                'description' => 'A clear, budget-aware template for school trips, groups, and booking steps.',
                'preview' => 'images/site-templates/ocean-blue.png',
                'theme_id' => $themes->get('ocean-blue')?->id,
                'pages' => $this->travelStarterPages('#2563eb', '#0f172a'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Business Travel',
                'slug' => 'business-travel',
                'description' => 'A concise template for corporate travel, meetings, events, and support plans.',
                'preview' => 'images/site-templates/city-escape.png',
                'theme_id' => $themes->get('urban-blue')?->id,
                'pages' => $this->travelStarterPages('#0ea5e9', '#172554'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Ski Holidays',
                'slug' => 'ski-holidays',
                'description' => 'A mountain-ready template for chalets, passes, lessons, and winter packages.',
                'preview' => 'images/site-templates/mountain-adventure.png',
                'theme_id' => $themes->get('wild-nature')?->id,
                'pages' => $this->travelStarterPages('#2f7d55', '#102a2f'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
            [
                'name' => 'Local Experiences',
                'slug' => 'local-experiences',
                'description' => 'A story-driven structure for local hosts, workshops, tours, and travel articles.',
                'preview' => 'images/site-templates/culture-journey.png',
                'theme_id' => $themes->get('heritage-rose')?->id,
                'pages' => $this->premiumEscapesPages('#be456b', '#431a2a'),
                'status' => SiteTemplate::STATUS_ACTIVE,
            ],
        ];

        foreach ($templates as $template) {
            SiteTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }

    private function travelStarterPages(string $primary, string $dark): array
    {
        return [
            $this->page('Home', 'home', 'Travel made simple', [
                $this->heroSection(
                    'Travel made simple',
                    'Showcase destinations, seasonal offers, and booking paths from one modern page.',
                    'View destinations',
                    '/destinations',
                    $primary,
                    $dark
                ),
                $this->dynamicSection('Popular Destinations', 'featured-destinations', ['limit' => 6]),
                $this->dynamicSection('This Month Offers', 'special-offers', ['limit' => 3]),
            ]),
            $this->page('Destinations', 'destinations', 'Explore curated destinations', [
                $this->simpleIntroSection('Explore curated destinations', 'Build trust with destination collections, country highlights, and clear travel cards.', $dark),
                $this->dynamicSection('All Destinations', 'destination-grid', ['source' => 'latest', 'limit' => 9]),
                $this->faqSection(),
            ]),
            $this->page('Offers', 'offers', 'Find the right offer', [
                $this->simpleIntroSection('Find the right offer', 'Promote limited-time packages, seasonal discounts, and custom travel experiences.', $dark),
                $this->dynamicSection('Latest Offers', 'offer-grid', ['source' => 'latest', 'limit' => 9]),
                $this->countdownSection($primary),
            ]),
            $this->page('Contact', 'contact', 'Plan your next trip', [
                $this->simpleIntroSection('Plan your next trip', 'Let visitors contact the agency and find your location quickly.', $dark),
                $this->contactSection($primary),
            ]),
        ];
    }

    private function signatureTravelPages(): array
    {
        return [
            $this->page('Home', 'home', 'Thoughtful journeys, designed around you', [
                $this->node('search-hero', [
                    'eyebrow' => 'YOUR JOURNEY STARTS HERE',
                    'title' => 'Go further. Travel deeper.',
                    'description' => 'Thoughtful journeys shaped by local experts and designed entirely around you.',
                    'searchType' => 'destinations',
                    'placeholder' => 'Where would you like to go?',
                    'buttonText' => 'Explore',
                    'backgroundImage' => '/images/site-templates/mountain-adventure.png',
                ]),
                $this->node('destination-carousel', [
                    'title' => 'Places that stay with you',
                    'limit' => 6,
                    'source' => 'featured',
                ]),
                $this->node('special-offers', [
                    'title' => 'Journeys worth taking now',
                    'limit' => 3,
                ]),
                $this->node('feature-grid', [
                    'eyebrow' => 'WHY TRAVEL WITH US',
                    'title' => 'Every detail, thoughtfully handled',
                    'items' => "compass|Local expertise|Travel with people who know each place deeply.\nheart|Designed for you|Every journey is shaped around your interests and rhythm.\ncheck-circle|Support that travels|We are with you before, during and after your trip.",
                    'columns' => 3,
                ]),
                $this->node('stats-counter', [
                    'items' => "15+|Years of experience\n40+|Destinations\n2,500+|Happy travellers\n24/7|Local support",
                ]),
                $this->node('testimonials', [
                    'eyebrow' => 'TRAVELLER STORIES',
                    'title' => 'Travel that stays with you',
                    'items' => "An unforgettable journey from beginning to end.|Amelia R.|London|5\nEvery detail felt personal and effortless.|Daniel M.|Toronto|5\nWe discovered places we would never have found alone.|Sofia K.|Madrid|5",
                ]),
                $this->node('trust-logos', [
                    'title' => 'Trusted by travellers and travel partners',
                    'items' => "IATA|\nTravelife|\nTripadvisor|",
                ]),
                $this->node('cta-banner', [
                    'eyebrow' => 'START PLANNING',
                    'title' => 'Your next story starts here',
                    'text' => 'Tell us what you are dreaming of and our travel designers will shape the journey with you.',
                    'buttonText' => 'Plan my journey',
                    'url' => '/contact',
                ]),
            ]),
            $this->catalogPage('Destinations', 'destinations', '/destinations', 'Explore remarkable places', [
                $this->signatureHero('EXPLORE THE WORLD', 'Places that move you', 'Discover handpicked destinations with local insight and room for genuine connection.'),
                $this->node('destination-grid', [
                    'title' => 'Choose your next chapter',
                    'source' => 'latest',
                    'limit' => 9,
                    'columns' => 3,
                ]),
                $this->node('feature-grid', [
                    'eyebrow' => 'TRAVEL YOUR WAY',
                    'title' => 'More than a place on the map',
                    'items' => "compass|Local perspective|See each place through people who know it best.\nheart|Your own pace|Balance iconic sights with time to explore freely.\ncheck-circle|Thoughtful choices|Stay and travel with partners we know and trust.",
                    'columns' => 3,
                ]),
                $this->signatureCta('Found somewhere inspiring?', 'Let us turn the destination into a journey designed around you.', 'Plan my trip'),
            ]),
            $this->catalogPage('Offers', 'offers', '/offers', 'Curated journeys and special offers', [
                $this->signatureHero('CURATED JOURNEYS', 'Travel offers, made personal', 'Explore inspiring itineraries, then adapt the pace, stays, and experiences with our travel designers.'),
                $this->node('offer-grid', [
                    'title' => 'Journeys worth taking',
                    'source' => 'latest',
                    'limit' => 9,
                    'columns' => 3,
                ]),
                $this->node('offer-comparison', [
                    'title' => 'Compare signature journeys',
                    'limit' => 3,
                    'buttonText' => 'View journey',
                ]),
                $this->signatureCta('Need something different?', 'Every offer can be the beginning of a journey shaped entirely around you.', 'Create my journey'),
            ]),
            $this->page('About', 'about', 'Meet the people behind your journey', [
                $this->signatureHero('OUR STORY', 'Travel should feel personal', 'We pair deep local knowledge with thoughtful service to create journeys that reflect who you are.'),
                $this->node('image-text', [
                    'eyebrow' => 'HOW WE WORK',
                    'title' => 'Local insight. Personal design.',
                    'text' => 'Our travel designers listen first, then build each journey with trusted local partners and space for genuine discovery.',
                    'image' => '/images/site-templates/culture-journey.png',
                    'imageAlt' => 'A locally designed travel experience',
                    'imagePosition' => 'left',
                    'buttonText' => 'Talk to our team',
                    'url' => '/contact',
                ]),
                $this->node('feature-grid', [
                    'eyebrow' => 'OUR PROMISE',
                    'title' => 'Designed with care, delivered with confidence',
                    'items' => "compass|Rooted locally|We work with people who know each destination deeply.\nheart|Entirely personal|Your interests and travel style shape every decision.\ncheck-circle|Responsible choices|We favour meaningful experiences and trusted partners.",
                    'columns' => 3,
                ]),
                $this->node('stats-counter', ['items' => "15+|Years of experience\n40+|Destinations\n2,500+|Happy travellers\n24/7|Local support"]),
                $this->node('testimonials', [
                    'eyebrow' => 'WHY IT MATTERS',
                    'title' => 'Journeys remembered for the right reasons',
                    'items' => "We felt looked after without ever feeling rushed.|Emma T.|Manchester|5\nThe local knowledge transformed our trip.|Noah B.|Amsterdam|5",
                ]),
                $this->signatureCta(),
            ]),
            $this->page('Contact', 'contact', 'Plan your next journey with us', [
                $this->signatureHero('LET US BEGIN', 'Where would you like to go?', 'Share the first idea. Our local travel designers will help shape everything that follows.'),
                $this->node('contact-info', [
                    'title' => 'Talk to a travel designer',
                    'text' => 'Use the agency contact details below to start planning.',
                    'email' => '',
                    'phone' => '',
                    'address' => '',
                ]),
                $this->node('contact-form', [
                    'title' => 'Tell us about your dream journey',
                    'subtitle' => 'This form is a design preview in Phase 3 and does not send or store messages.',
                    'nameLabel' => 'Your name',
                    'emailLabel' => 'Email address',
                    'messageLabel' => 'Where do you want to go?',
                    'buttonText' => 'Start planning',
                    'actionUrl' => '',
                ]),
                $this->node('map', ['address' => 'Marrakech, Morocco', 'height' => 440, 'zoom' => 12, 'borderRadius' => 18]),
                $this->node('newsletter', [
                    'title' => 'Stories worth travelling for',
                    'text' => 'Occasional destination ideas and journeys from our travel designers.',
                    'placeholder' => 'Email address',
                    'buttonText' => 'Subscribe',
                ]),
            ]),
            $this->page('FAQ', 'faq', 'Travel questions, clearly answered', [
                $this->signatureHero('GOOD TO KNOW', 'Questions before you travel', 'Clear answers about planning, customisation, support, and what happens next.'),
                $this->node('accordion', [
                    'title' => 'Planning your journey',
                    'items' => "How does planning work?|Tell us your ideas, timing, and travel style. We will turn them into a tailored proposal.\nCan I change an itinerary?|Yes. Dates, stays, pacing, and experiences can all be adapted before confirmation.\nDo you offer support while travelling?|Yes. Your agency remains available for practical help throughout the journey.\nAre the prices final?|Displayed prices are indicative until the agency confirms availability and your final itinerary.",
                ]),
                $this->signatureCta('Still have a question?', 'Our travel designers are ready to help.', 'Ask our team'),
            ]),
            $this->hiddenPage('Privacy Policy', 'privacy-policy', 'How we respect your privacy', [
                $this->signatureHero('LEGAL', 'Privacy Policy', 'A clear starting point for explaining how your agency handles personal information.'),
                $this->legalSection('<h2>1. Information we collect</h2><p>Replace this template text with the information your agency collects through its website and direct communications.</p><h2>2. How information is used</h2><p>Explain the purposes, legal basis, retention period, and service providers involved.</p><h2>3. Your choices</h2><p>Describe how visitors can request access, correction, or deletion and how they can contact your agency.</p><p><strong>Important:</strong> This template is not legal advice. Review it with a qualified professional before publishing.</p>'),
            ]),
            $this->hiddenPage('Terms and Conditions', 'terms-and-conditions', 'Website terms and travel conditions', [
                $this->signatureHero('LEGAL', 'Terms and Conditions', 'A structured starting point for your website and travel service terms.'),
                $this->legalSection('<h2>1. Using this website</h2><p>Define acceptable use, content ownership, and the limits of information displayed online.</p><h2>2. Quotes and travel services</h2><p>Explain that availability, itinerary, inclusions, price, payment, cancellation, and supplier terms must be confirmed directly by the agency.</p><h2>3. Responsibility</h2><p>Add the rules and consumer protections that apply to your business and operating country.</p><p><strong>Important:</strong> This template is not legal advice. Review it with a qualified professional before publishing.</p>'),
            ]),
        ];
    }

    private function signatureHero(string $eyebrow, string $title, string $description): array
    {
        return $this->node('hero', [
            'eyebrow' => $eyebrow,
            'title' => $title,
            'description' => $description,
            'buttonText' => '',
            'backgroundMode' => 'color',
            'variant' => 'left',
            'padding' => 88,
        ]);
    }

    private function signatureCta(string $title = 'Your next story starts here', string $text = 'Tell us what you are dreaming of and we will shape the journey with you.', string $button = 'Plan my journey'): array
    {
        return $this->node('cta-banner', ['eyebrow' => 'START PLANNING', 'title' => $title, 'text' => $text, 'buttonText' => $button, 'url' => '/contact']);
    }

    private function legalSection(string $html): array
    {
        return $this->node('section', ['paddingTop' => 72, 'paddingBottom' => 72], [
            $this->container('layout', ['maxWidth' => 860, 'paddingLeft' => 24, 'paddingRight' => 24], [
                $this->node('richtext', ['html' => $html, 'fontSize' => 17, 'lineHeight' => 1.75, 'padding' => 0]),
            ]),
        ]);
    }

    private function hiddenPage(string $title, string $slug, string $metaTitle, array $structure): array
    {
        $page = $this->page($title, $slug, $metaTitle, $structure);
        $page['include_in_menu'] = false;

        return $page;
    }

    private function catalogPage(string $title, string $slug, string $menuUrl, string $metaTitle, array $structure): array
    {
        $page = $this->page($title, $slug, $metaTitle, $structure);
        $page['menu_url'] = $menuUrl;

        return $page;
    }

    private function premiumEscapesPages(string $primary, string $dark): array
    {
        return [
            $this->page('Home', 'home', 'Premium stays and private tours', [
                $this->heroSection(
                    'Premium stays and private tours',
                    'Present luxury escapes, private guides, and handpicked experiences with a bold first impression.',
                    'View offers',
                    '/offers',
                    $primary,
                    $dark
                ),
                $this->dynamicSection('Featured Escapes', 'featured-destinations', ['limit' => 3]),
                $this->dynamicSection('Limited Offers', 'special-offers', ['limit' => 3]),
            ]),
            $this->page('Destinations', 'destinations', 'Signature destinations', [
                $this->simpleIntroSection('Signature destinations', 'Feature your strongest travel regions and inspire visitors to start planning.', $dark),
                $this->dynamicSection('Destination Collection', 'destination-grid', ['source' => 'featured', 'limit' => 6]),
                $this->gallerySection(),
            ]),
            $this->page('Offers', 'offers', 'Luxury packages', [
                $this->simpleIntroSection('Luxury packages', 'Use this page to present private tours, premium packages, and seasonal promotions.', $dark),
                $this->dynamicSection('Special Packages', 'special-offers', ['limit' => 6]),
                $this->countdownSection($primary),
            ]),
            $this->page('Contact', 'contact', 'Request a private quote', [
                $this->simpleIntroSection('Request a private quote', 'Invite travelers to share dates, destination ideas, and budget preferences.', $dark),
                $this->contactSection($primary),
            ]),
        ];
    }

    private function compactTravelPages(string $title, string $subtitle, string $primary, string $dark, array $pageNames): array
    {
        $home = $pageNames[0] ?? 'Home';
        $listing = $pageNames[1] ?? 'Trips';
        $contact = $pageNames[2] ?? 'About';

        return [
            $this->page($home, \Illuminate\Support\Str::slug($home), $title, [
                $this->heroSection(
                    $title,
                    $subtitle,
                    'View trips',
                    '/trips',
                    $primary,
                    $dark
                ),
                $this->dynamicSection('Featured Trips', 'featured-destinations', ['limit' => 3]),
                $this->dynamicSection('Current Offers', 'special-offers', ['limit' => 2]),
            ]),
            $this->page($listing, \Illuminate\Support\Str::slug($listing), 'Curated '.$listing, [
                $this->simpleIntroSection('Curated '.$listing, 'Present the most important options with direct booking paths and clear traveler expectations.', $dark),
                $this->dynamicSection($listing.' Collection', 'destination-grid', ['source' => 'featured', 'limit' => 6]),
                $this->faqSection(),
            ]),
            $this->page($contact, \Illuminate\Support\Str::slug($contact), 'Plan with us', [
                $this->simpleIntroSection('Plan with us', 'Use this page to explain the experience and invite travelers to request a custom quote.', $dark),
                $this->contactSection($primary),
            ]),
        ];
    }

    private function page(string $title, string $slug, string $metaTitle, array $structure): array
    {
        return [
            'title' => $title,
            'slug' => $slug,
            'status' => Page::STATUS_DRAFT,
            'include_in_menu' => true,
            'meta' => [
                'title' => $metaTitle,
                'description' => 'Travel website page generated from a site template.',
            ],
            'structure' => $structure,
        ];
    }

    private function heroSection(string $title, string $subtitle, string $button, string $url, string $primary, string $dark): array
    {
        return $this->node('section', [
            'backgroundColor' => $dark,
            'textColor' => '#ffffff',
            'paddingTop' => 82,
            'paddingBottom' => 82,
            'overflow' => 'hidden',
        ], [
            $this->layout([
                $this->grid([
                    $this->container('content', [
                        'gridSpan' => 7,
                        'display' => 'flex',
                        'flexDirection' => 'column',
                        'gap' => 18,
                        'maxWidth' => 'none',
                    ], [
                        $this->headline($title, 54, '#ffffff'),
                        $this->paragraph($subtitle, 20, '#e2e8f0'),
                        $this->button($button, $url, $primary),
                    ]),
                    $this->container('card', [
                        'gridSpan' => 5,
                        'backgroundColor' => '#1e293b',
                        'borderRadius' => 24,
                        'paddingTop' => 24,
                        'paddingBottom' => 24,
                        'paddingLeft' => 24,
                        'paddingRight' => 24,
                        'minHeight' => 320,
                    ], [
                        $this->node('image', [
                            'alt' => 'Destination image',
                            'height' => 280,
                            'backgroundColor' => '#dbeafe',
                            'borderColor' => '#dbeafe',
                            'borderStyle' => 'solid',
                        ]),
                    ]),
                ]),
            ]),
        ]);
    }

    private function simpleIntroSection(string $title, string $subtitle, string $dark): array
    {
        return $this->node('section', [
            'backgroundColor' => '#ffffff',
            'paddingTop' => 64,
            'paddingBottom' => 32,
        ], [
            $this->layout([
                $this->container('content', [
                    'display' => 'flex',
                    'flexDirection' => 'column',
                    'gap' => 14,
                    'maxWidth' => 760,
                ], [
                    $this->headline($title, 42, $dark),
                    $this->paragraph($subtitle, 18, '#64748b'),
                ]),
            ]),
        ]);
    }

    private function dynamicSection(string $title, string $type, array $props): array
    {
        return $this->node('section', [
            'backgroundColor' => '#ffffff',
            'paddingTop' => 56,
            'paddingBottom' => 56,
        ], [
            $this->layout([
                $this->container('content', [
                    'display' => 'flex',
                    'flexDirection' => 'column',
                    'gap' => 20,
                ], [
                    $this->headline($title, 34, '#0f172a'),
                    $this->node($type, $props + ['title' => null]),
                ]),
            ]),
        ]);
    }

    private function faqSection(): array
    {
        return $this->node('section', [
            'backgroundColor' => '#f8fafc',
            'paddingTop' => 56,
            'paddingBottom' => 56,
        ], [
            $this->layout([
                $this->node('faq', [
                    'title' => 'Travel Questions',
                    'items' => "Can we customize the trip?|Yes. Adjust dates, hotels, and activities with the agency.\nAre offers updated?|Yes. Special offers can be managed from the Offers module.",
                ]),
            ]),
        ]);
    }

    private function countdownSection(string $primary): array
    {
        return $this->node('section', [
            'backgroundColor' => '#f8fafc',
            'paddingTop' => 48,
            'paddingBottom' => 64,
        ], [
            $this->layout([
                $this->node('countdown', [
                    'label' => 'Seasonal offer ends in',
                    'targetDate' => '2026-12-31T23:59',
                    'backgroundColor' => '#0f172a',
                    'textColor' => '#ffffff',
                    'accentColor' => $primary,
                ]),
            ]),
        ]);
    }

    private function gallerySection(): array
    {
        return $this->node('section', [
            'backgroundColor' => '#ffffff',
            'paddingTop' => 48,
            'paddingBottom' => 64,
        ], [
            $this->layout([
                $this->node('gallery', [
                    'images' => '',
                    'columns' => 3,
                    'gap' => 18,
                    'height' => 220,
                    'borderRadius' => 16,
                ]),
            ]),
        ]);
    }

    private function contactSection(string $primary): array
    {
        return $this->node('section', [
            'backgroundColor' => '#f8fafc',
            'paddingTop' => 48,
            'paddingBottom' => 64,
        ], [
            $this->layout([
                $this->grid([
                    $this->container('card', [
                        'gridSpan' => 6,
                        'backgroundColor' => '#ffffff',
                        'borderRadius' => 18,
                        'paddingTop' => 16,
                        'paddingBottom' => 16,
                        'paddingLeft' => 16,
                        'paddingRight' => 16,
                        'borderWidth' => 1,
                        'borderColor' => '#e2e8f0',
                    ], [
                        $this->node('contact-form', [
                            'title' => 'Contact our agency',
                            'subtitle' => 'Tell us where you want to go and our team will prepare the right proposal.',
                            'buttonText' => 'Send request',
                            'buttonColor' => $primary,
                        ]),
                    ]),
                    $this->container('card', [
                        'gridSpan' => 6,
                        'backgroundColor' => '#ffffff',
                        'borderRadius' => 18,
                        'paddingTop' => 16,
                        'paddingBottom' => 16,
                        'paddingLeft' => 16,
                        'paddingRight' => 16,
                        'borderWidth' => 1,
                        'borderColor' => '#e2e8f0',
                    ], [
                        $this->node('map', [
                            'address' => 'Marrakech, Morocco',
                            'height' => 460,
                            'borderRadius' => 14,
                        ]),
                    ]),
                ]),
            ]),
        ]);
    }

    private function layout(array $children): array
    {
        return $this->container('layout', [
            'maxWidth' => 1180,
            'paddingLeft' => 24,
            'paddingRight' => 24,
        ], $children);
    }

    private function grid(array $children): array
    {
        return $this->container('grid', [
            'display' => 'grid',
            'gridColumns' => 12,
            'gap' => 28,
        ], $children);
    }

    private function container(string $role, array $props = [], array $children = []): array
    {
        return $this->node('container', ['containerRole' => $role] + $props, $children);
    }

    private function headline(string $text, int $size, string $color): array
    {
        return $this->node('text', [
            'text' => $text,
            'fontSize' => $size,
            'fontWeight' => 800,
            'lineHeight' => 1.08,
            'color' => $color,
            'padding' => 0,
        ]);
    }

    private function paragraph(string $text, int $size, string $color): array
    {
        return $this->node('text', [
            'text' => $text,
            'fontSize' => $size,
            'fontWeight' => 400,
            'lineHeight' => 1.55,
            'color' => $color,
            'padding' => 0,
        ]);
    }

    private function button(string $text, string $url, string $primary): array
    {
        return $this->node('button', [
            'text' => $text,
            'url' => $url,
            'backgroundColor' => $primary,
            'textColor' => '#ffffff',
        ]);
    }

    private function node(string $type, array $props = [], array $children = []): array
    {
        return [
            'id' => 'template-'.$type.'-'.substr(md5($type.serialize($props).count($children)), 0, 8),
            'type' => $type,
            'props' => $props,
            'children' => $children,
        ];
    }
}
