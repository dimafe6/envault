<?php

namespace Tests\Feature\Users;

use App\User;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTest extends TestCase
{
    protected $authenticatedUser;

    /** @test */
    public function can_create_user()
    {
        $userToCreate = factory(User::class)->make();

        Livewire::test('users.create')
            ->set('email', $userToCreate->email)
            ->set('firstName', $userToCreate->first_name)
            ->set('lastName', $userToCreate->last_name)
            ->call('store')
            ->assertEmitted('user.created')
            ->assertSet('email', '')
            ->assertSet('firstName', '')
            ->assertSet('lastName', '');

        $this->assertDatabaseHas('users', [
            'email' => $userToCreate->email,
            'first_name' => $userToCreate->first_name,
            'last_name' => $userToCreate->last_name,
        ]);
    }

    /** @test */
    public function email_is_email()
    {
        Livewire::test('users.create')
            ->set('email', 'email')
            ->call('store')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function email_is_required()
    {
        Livewire::test('users.create')
            ->call('store')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function email_is_unique()
    {
        Livewire::test('users.create')
            ->set('email', $this->authenticatedUser->email)
            ->call('store')
            ->assertHasErrors(['email' => 'unique']);
    }

    /** @test */
    public function first_name_is_required()
    {
        Livewire::test('users.create')
            ->call('store')
            ->assertHasErrors(['firstName' => 'required']);
    }

    /** @test */
    public function last_name_is_required()
    {
        Livewire::test('users.create')
            ->call('store')
            ->assertHasErrors(['lastName' => 'required']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticatedUser = factory(User::class)->create([
            'role' => 'owner',
        ]);

        Livewire::actingAs($this->authenticatedUser);
    }
}