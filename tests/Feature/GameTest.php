<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\Score;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_the_game(): void
    {
        $this->get(route('game.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_the_game_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('game.index'))
            ->assertOk()
            ->assertViewIs('game.index');
    }

    public function test_base_characters_are_attached_to_the_user_on_first_visit(): void
    {
        $user = User::factory()->create();
        $base = Character::factory()->base()->create();
        Character::factory()->create(); // perso non-base, ne doit pas être attaché automatiquement

        $this->actingAs($user)->get(route('game.index'))->assertOk();

        $this->assertTrue($user->fresh()->hasCharacter($base));
        $this->assertCount(1, $user->fresh()->characters);
    }

    public function test_a_score_is_saved_and_coins_are_credited(): void
    {
        $user = User::factory()->create(['coins' => 0]);
        $character = Character::factory()->create();

        $response = $this->actingAs($user)->postJson(route('game.score'), [
            'score'           => 1500,
            'coins_collected' => 30,
            'difficulty'      => 'normal',
            'duration'        => 90,
            'character_id'    => $character->id,
        ]);

        $response->assertOk()->assertJson([
            'success'    => true,
            'new_coins'  => 30,
            'chest_type' => 'normal',
        ]);

        $this->assertDatabaseHas('scores', [
            'user_id'      => $user->id,
            'character_id' => $character->id,
            'score'        => 1500,
        ]);
        $this->assertEquals(30, $user->fresh()->coins);
    }

    public function test_a_hard_run_grants_a_legendary_chest(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('game.score'), [
            'score'           => 800,
            'coins_collected' => 10,
            'difficulty'      => 'hard',
            'duration'        => 60,
            'character_id'    => null,
        ]);

        $response->assertOk()->assertJson(['chest_type' => 'legendary']);
    }

    public function test_score_submission_is_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('game.score'), [
            'score'           => -5,
            'coins_collected' => 10,
            'difficulty'      => 'medium', // invalide : doit être normal|hard
            'duration'        => 60,
        ])->assertJsonValidationErrors(['score', 'difficulty']);
    }

    public function test_leaderboard_lists_top_scores_in_descending_order(): void
    {
        $userA = User::factory()->create(['name' => 'Alice']);
        $userB = User::factory()->create(['name' => 'Bob']);

        Score::factory()->for($userA)->create(['score' => 100]);
        Score::factory()->for($userB)->create(['score' => 900]);

        $response = $this->get(route('leaderboard.index'));

        $response->assertOk();
        $topScores = $response->viewData('topScores');

        $this->assertEquals('Bob', $topScores->first()->user->name);
        $this->assertEquals(900, $topScores->first()->score);
    }
}
