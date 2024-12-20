<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTravelTest extends TestCase
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

    public function test_public_user_cannot_access_adding_travel()
    {
        $response=$this->postJson('/api/v1/admin/travels');
        $response->assertStatus(401);
    }
    public function test_non_admin_user_cannot_access_adding_travel()
    {
        $this->seed(RoleSeeder::class);
        $user=User::factory()->create();
        $user->roles()->attach(Role::where('name','editor')->value('id'));
        $response=$this->actingAs($user)->postJson('/api/v1/admin/travels');
        $response->assertStatus(403);
    }
    public function test_saves_travel_successfully_with_valid_data()
    {
        $this->seed(RoleSeeder::class);
        $user=User::factory()->create();
        $user->roles()->attach(Role::where('name','admin')->value('id'));
        $response=$this->actingAs($user)->postJson('/api/v1/admin/travels',[
            'name'=>'travel name'

        ]);
        $response->assertStatus(422);
        $response=$this->actingAs($user)->postJson('/api/v1/admin/travels',[
            'name'=>'travel name',
            'is_public'=> 1,
            'description'=>'some description',
            'number_of_days'=>5

        ]);
        $response->assertStatus(201);

        $response=$this->get('/api/v1/travels');
        $response->assertJsonFragment(['name'=>'travel name']);


    }

    public function test_updates_travel_successfully_with_valid_data()
    {
        $this->seed(RoleSeeder::class);
        $user=User::factory()->create();
        $user->roles()->attach(Role::where('name','editor')->value('id'));
        $travel=Travel::factory()->create();
        $respone=$this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id,[
            'name'=>'Travel name'
        ]);
        $respone->assertStatus(422);

        $respone=$this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id,[
            'name'=>'updated',
            'is_public'=>1,
            'description'=>'updated',
            'number_of_days'=>5
        ]);

        $respone->assertStatus(200);
        $response=$this->get('/api/v1/travels');
        $response->assertJsonFragment(['name'=>'updated']);

    }
}
