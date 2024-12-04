<?php

namespace Tests\Feature;

use App\Models\Tour;
use Tests\TestCase;
use App\Models\Travel;
use Database\Factories\TravelFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TourListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_tours_list_by_travel_slug_returns_correct_tours()
    {
        $travel=Travel::factory()->create();
        $tour=Tour::factory()->create(['travel_id'=>$travel->id]);
        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonFragment(['id'=>$tour->id]);


    }

    public function test_tour_price_is_shown_correctly()
    {
        $travel=Travel::factory()->create();
        $tour=Tour::factory()->create([
            'travel_id'=>$travel->id,
            'price'=>123.45
        ]);
        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonFragment(['price'=>'123.45']);


    }

    public function test_tours_list_returns_paginated_data_correctly(): void
    {
        $travel=Travel::factory()->create();
        $tour=Tour::factory(16)->create(['travel_id'=>$travel->id]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');
        $response->assertStatus(200);
        $response->assertJsonCount(15,'data');
        $response->assertJsonPath('meta.last_page',2);
    }

    public function test_tours_list_sorts_by_starting_date_correctly()
    {
        $travel=Travel::factory()->create();
        $earliertour=Tour::factory()->create([
            'travel_id'=>$travel->id,
            'starting_date'=>now(),
            'ending_date'=>now()->addDays(1)
        ]);
        $latertour=Tour::factory()->create([
            'travel_id'=>$travel->id,
            'starting_date'=>now()->addDays(2),
            'ending_date'=>now()->addDays(3)
        ]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id',$earliertour->id);
        $response->assertJsonPath('data.1.id',$latertour->id);
    }

    public function test_tours_list_sorts_by_price_correctly() //testing that if there is 2 tours with the same price also
    {
        $travel=Travel::factory()->create();
        $expensiveTour=Tour::factory()->create([
            'travel_id'=>$travel->id,
            'price'=>200
        ]
        );
        $cheaperEarlierTour=Tour::factory()->create([
            'travel_id'=>$travel->id,
            'price'=>100,
            'starting_date'=>now(),
            'ending_date'=>now()->addDays(1)
        ]
        );
        $cheaperLaterTour=Tour::factory()->create([
            'travel_id'=>$travel->id,
            'price'=>100,
            'starting_date'=>now()->addDays(2),
            'ending_date'=>now()->addDays(3)
        ]
        );

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours?sortby=price & sortorder=asc');
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id',$cheaperEarlierTour->id);
        $response->assertJsonPath('data.1.id',$cheaperLaterTour->id);
        $response->assertJsonPath('data.2.id',$expensiveTour->id);

    }

    public function test_tours_list_filters_by_price_correctly()
    {
        $travel=Travel::factory()->create();
        $expensiveTour=Tour::factory()->create([
            'travel_id'=>$travel->id,
            'price'=>200
        ]
        );
        $cheaperEarlierTour=Tour::factory()->create([
            'travel_id'=>$travel->id,
            'price'=>100
        ]
        );

        $endpoint='/api/v1/travels/'.$travel->slug.'/tours';

        $response = $this->get($endpoint.'?pricefrom=100');
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');      //since i test the price that starts from 100 i expect 2 objects appear
        $response->assertJsonFragment(['id'=>$cheaperEarlierTour->id]);   //fragment not path because i want to check that there is 2 id with these values, not important to me the sort
        $response->assertJsonFragment(['id'=>$expensiveTour->id]);

        $response = $this->get($endpoint.'?pricefrom=150');
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');      
        $response->assertJsonMissing(['id'=>$cheaperEarlierTour->id]);   
        $response->assertJsonFragment(['id'=>$expensiveTour->id]);

        $response = $this->get($endpoint.'?pricefrom=250');
        $response->assertJsonCount(0,'data');  

        $response = $this->get($endpoint.'?priceto=200');
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');      
        $response->assertJsonFragment(['id'=>$cheaperEarlierTour->id]);   
        $response->assertJsonFragment(['id'=>$expensiveTour->id]);

        $response = $this->get($endpoint.'?priceto=150');
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');      
        $response->assertJsonMissing(['id'=>$expensiveTour->id]);   
        $response->assertJsonFragment(['id'=>$cheaperEarlierTour->id]);

        $response = $this->get($endpoint.'?priceto=50');
        $response->assertJsonCount(0,'data'); 
        
        $response = $this->get($endpoint.'?pricefrom=150 & priceto=250');
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');      
        $response->assertJsonMissing(['id'=>$cheaperEarlierTour->id]);   
        $response->assertJsonFragment(['id'=>$expensiveTour->id]);


        
    }
    public function test_tours_list_filters_by_starting_date_correctly()
    {
        $travel=Travel::factory()->create();
        $earliertour=Tour::factory()->create([
            'travel_id'=>$travel->id,
            'starting_date'=>now(),
            'ending_date'=>now()->addDays(1)
        ]);
        $latertour=Tour::factory()->create([
            'travel_id'=>$travel->id,
            'starting_date'=>now()->addDays(2),
            'ending_date'=>now()->addDays(3)
        ]);

        $endpoint='/api/v1/travels/'.$travel->slug.'/tours';

        $response = $this->get($endpoint.'?datefrom='.now());
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');      //since i test the price that starts from 100 i expect 2 objects appear
        $response->assertJsonFragment(['id'=>$earliertour->id]);   //fragment not path because i want to check that there is 2 id with these values, not important to me the sort
        $response->assertJsonFragment(['id'=>$latertour->id]);

        $response = $this->get($endpoint.'?datefrom='.now()->addDay());
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');      
        $response->assertJsonMissing(['id'=>$earliertour->id]);   
        $response->assertJsonFragment(['id'=>$latertour->id]);

        $response = $this->get($endpoint.'?datefrom='.now()->addDays(5));
        $response->assertJsonCount(0,'data');  

        $response = $this->get($endpoint.'?dateto='.now()->addDays(5));
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');      
        $response->assertJsonFragment(['id'=>$earliertour->id]);   
        $response->assertJsonFragment(['id'=>$latertour->id]);

        $response = $this->get($endpoint.'?dateto='.now()->addDay());
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');      
        $response->assertJsonMissing(['id'=>$latertour->id]);   
        $response->assertJsonFragment(['id'=>$earliertour->id]);

        $response = $this->get($endpoint.'?dateto='.now()->subDay());
        $response->assertJsonCount(0,'data'); 
        
        $response = $this->get($endpoint.'?datefrom='.now()->addDay().' & dateto='. now()->addDays(5));
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');      
        $response->assertJsonMissing(['id'=>$earliertour->id]);   
        $response->assertJsonFragment(['id'=>$latertour->id]);

        
    }

    public function test_tour_list_returns_validation_errors()
    {
        $travel=Travel::factory()->create();
        $response = $this->getJson('/api/v1/travels/'.$travel->slug.'/tours?datefrom=abvd');
        $response->assertStatus(422);
        $response = $this->getJson('/api/v1/travels/'.$travel->slug.'/tours?pricefrom=abvd');
        $response->assertStatus(422);
    }


}
