<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Travel;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTourTest extends TestCase
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
    public function test_public_user_cannot_access_adding_tour()
    {
        $travel=Travel::factory()->create();
        $response=$this->postJson('/api/v1/admin/travels/'.$travel->id.'/tours');
        $response->assertStatus(401);
    }
    public function test_non_admin_user_cannot_access_adding_travel()
    {
        $this->seed(RoleSeeder::class);
        $user=User::factory()->create();
        $user->roles()->attach(Role::where('name','editor')->value('id'));
        $travel=Travel::factory()->create();
        $response=$this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->id.'/tours');
        $response->assertStatus(403);
    }
    public function test_saves_travel_successfully_with_valid_data()
    {
        $this->seed(RoleSeeder::class);
        $user=User::factory()->create();
        $user->roles()->attach(Role::where('name','admin')->value('id'));
        $travel=Travel::factory()->create();
        $response=$this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->id.'/tours',[
            'name'=>'travel name'

        ]);
        $response->assertStatus(422);
        $response=$this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->id.'/tours',[
            'name'=>'travel name',
            'starting_date'=>now()->toDateString(),
            'ending_date'=>now()->addDay()->toDateString(),
            'price'=>900

        ]);
        $response->assertStatus(201);

        $response=$this->get('/api/v1/travels/'. $travel->slug.'/tours');
        $response->assertJsonFragment(['name'=>'travel name']);


    }
}
