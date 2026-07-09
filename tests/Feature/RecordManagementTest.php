<?php

namespace Tests\Feature;

use App\Models\Record;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordManagementTest extends TestCase
{
    use RefreshDatabase;

    private function verified(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email_verified_at' => now(),
        ], $attributes));
    }

    private function makeRecord(array $attributes = []): Record
    {
        return Record::create(array_merge([
            'record_type' => 'pig',
            'pig_code' => 'PIG-'.fake()->unique()->numberBetween(1000, 9999),
            'gender' => 'Dume',
            'breed' => 'Duroc',
            'birth_date' => '2026-01-01',
            'pen_number' => 'Banda A',
            'status' => 'Anakuwa',
        ], $attributes));
    }

    public function test_store_persists_all_pig_fields_including_castration_and_age(): void
    {
        $user = $this->verified();

        $this->actingAs($user)->post('/records', [
            'record_type' => 'pig',
            'pig_code' => 'PIG-001',
            'gender' => 'Dume',
            'breed' => 'Duroc',
            'castration_status' => 'Amehasiwa',
            'age_manual' => '2 Miezi',
            'birth_date' => '2026-01-01',
            'pen_number' => 'Banda A',
            'status' => 'Anakuwa',
        ])->assertRedirect();

        $this->assertDatabaseHas('records', [
            'pig_code' => 'PIG-001',
            'castration_status' => 'Amehasiwa',
            'age_manual' => '2 Miezi',
            'user_id' => $user->id,
        ]);
    }

    public function test_update_modifies_an_existing_record(): void
    {
        $user = $this->verified();
        $record = $this->makeRecord([
            'pig_code' => 'PIG-100', 'gender' => 'Jike', 'breed' => 'Landrace', 'user_id' => $user->id,
        ]);

        $this->actingAs($user)->put("/records/{$record->id}", [
            'record_type' => 'pig',
            'pig_code' => 'PIG-100',
            'gender' => 'Jike',
            'breed' => 'Large White',
            'birth_date' => '2026-01-01',
            'status' => 'Mzazi',
        ])->assertRedirect();

        $this->assertDatabaseHas('records', [
            'id' => $record->id, 'breed' => 'Large White', 'status' => 'Mzazi',
        ]);
    }

    public function test_update_weight_appends_to_history(): void
    {
        $user = $this->verified();
        $record = $this->makeRecord(['pig_code' => 'PIG-200', 'user_id' => $user->id]);

        $this->actingAs($user)->put("/records/{$record->id}/weight", ['new_weight' => 45.5])
            ->assertRedirect();

        $record->refresh();
        $this->assertEquals(45.5, (float) $record->value);
        $this->assertCount(1, $record->weight_history);
        $this->assertEquals(45.5, $record->weight_history[0]['weight']);
    }

    public function test_user_cannot_update_another_users_record(): void
    {
        $owner = $this->verified();
        $intruder = $this->verified();
        $record = $this->makeRecord(['pig_code' => 'PIG-300', 'user_id' => $owner->id]);

        $this->actingAs($intruder)->put("/records/{$record->id}", [
            'record_type' => 'pig', 'pig_code' => 'PIG-300', 'gender' => 'Dume',
            'breed' => 'Hacked', 'birth_date' => '2026-01-01',
        ])->assertForbidden();

        $this->assertDatabaseMissing('records', ['breed' => 'Hacked']);
    }

    public function test_admin_can_update_any_record(): void
    {
        $owner = $this->verified();
        $admin = $this->verified(['is_admin' => true]);
        $record = $this->makeRecord(['pig_code' => 'PIG-400', 'user_id' => $owner->id]);

        $this->actingAs($admin)->put("/records/{$record->id}", [
            'record_type' => 'pig', 'pig_code' => 'PIG-400', 'gender' => 'Dume',
            'breed' => 'AdminEdit', 'birth_date' => '2026-01-01',
        ])->assertRedirect();

        $this->assertDatabaseHas('records', ['id' => $record->id, 'breed' => 'AdminEdit']);
    }

    public function test_duplicate_pig_code_is_rejected(): void
    {
        $user = $this->verified();
        $this->makeRecord(['pig_code' => 'DUP-1', 'user_id' => $user->id]);

        $this->actingAs($user)->post('/records', [
            'record_type' => 'pig', 'pig_code' => 'DUP-1', 'gender' => 'Jike',
            'breed' => 'Duroc', 'birth_date' => '2026-01-01',
        ])->assertSessionHasErrors('pig_code');
    }

    public function test_dashboard_and_records_index_render(): void
    {
        $user = $this->verified();

        $this->actingAs($user)->get('/dashboard')->assertOk();
        $this->actingAs($user)->get('/records')->assertOk();
    }

    public function test_index_scopes_records_to_owner(): void
    {
        $owner = $this->verified();
        $other = $this->verified();
        $this->makeRecord(['pig_code' => 'MINE-1', 'user_id' => $owner->id]);
        $this->makeRecord(['pig_code' => 'THEIRS-1', 'user_id' => $other->id]);

        $this->assertEquals(1, Record::query()->visibleTo($owner)->count());
        $this->assertEquals(2, Record::query()->visibleTo($this->verified(['is_admin' => true]))->count());
    }
}
