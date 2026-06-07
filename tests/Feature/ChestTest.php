<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChestTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_open_chests(): void
    {
        $this->postJson(route('chest.open'), ['type' => 'normal'])
            ->assertUnauthorized();
    }

    public function test_opening_a_normal_chest_costs_coins_and_grants_a_character(): void
    {
        $user = User::factory()->create(['coins' => 100]);
        Character::factory()->create(['rarity' => 'normal', 'probability' => 100]);

        $response = $this->actingAs($user)->postJson(route('chest.open'), ['type' => 'normal']);

        $response->assertOk()->assertJson(['success' => true, 'is_new' => true]);
        $this->assertEquals(50, $user->fresh()->coins);
        $this->assertCount(1, $user->fresh()->characters);
    }

    public function test_opening_a_normal_chest_without_enough_coins_fails_and_does_not_charge(): void
    {
        $user = User::factory()->create(['coins' => 10]);
        Character::factory()->create(['rarity' => 'normal', 'probability' => 100]);

        $response = $this->actingAs($user)->postJson(route('chest.open'), ['type' => 'normal']);

        $response->assertStatus(400)->assertJson(['success' => false]);
        $this->assertEquals(10, $user->fresh()->coins);
        $this->assertCount(0, $user->fresh()->characters);
    }

    public function test_legendary_chest_is_free_to_open(): void
    {
        $user = User::factory()->create(['coins' => 0]);
        Character::factory()->legendary()->create(['probability' => 100]);

        $response = $this->actingAs($user)->postJson(route('chest.open'), ['type' => 'legendary']);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertEquals(0, $user->fresh()->coins);
    }

    public function test_chest_request_validates_type(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('chest.open'), ['type' => 'mythic'])
            ->assertJsonValidationErrors(['type']);
    }

    public function test_normal_chest_refunds_when_no_character_is_available(): void
    {
        $user = User::factory()->create(['coins' => 100]);
        // Aucun personnage de rareté "normal" en base : le tirage échoue et doit rembourser.

        $response = $this->actingAs($user)->postJson(route('chest.open'), ['type' => 'normal']);

        $response->assertOk()->assertJson(['success' => false]);
        $this->assertEquals(100, $user->fresh()->coins);
    }
}
