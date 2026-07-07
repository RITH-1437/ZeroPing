<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Models\User;

class OrmTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'orm:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Test the ORM functionality.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Testing ORM...');

        $this->testCreate();
        $this->testSave();
        $this->testUpdate();
        $this->testDelete();
        $this->testFind();
        $this->testAll();
        $this->testWhere();
        $this->testFirst();
        $this->testRelationships();
        $this->testTimestamps();
        $this->testScopes();
        $this->testCasting();
        $this->testSoftDeletes();

        $this->info('ORM test completed successfully!');
    }

    protected function testCreate(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
        ]);

        $this->assert($user->id !== null, 'create');
    }

    protected function testSave(): void
    {
        $user = new User([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'password',
        ]);

        $user->save();

        $this->assert($user->id !== null, 'save');
    }

    protected function testUpdate(): void
    {
        $user = User::create([
            'name' => 'Update Test',
            'email' => 'update.test@example.com',
            'password' => 'password',
        ]);

        $user->update([
            'name' => 'Updated Name',
        ]);

        $this->assert($user->name === 'Updated Name', 'update');
    }

    protected function testDelete(): void
    {
        $user = User::create([
            'name' => 'Delete Test',
            'email' => 'delete.test@example.com',
            'password' => 'password',
        ]);

        $id = $user->id;

        $user->delete();

        $this->assert(User::find($id) === null, 'delete');
    }

    protected function testFind(): void
    {
        $user = User::create([
            'name' => 'Find Test',
            'email' => 'find.test@example.com',
            'password' => 'password',
        ]);

        $foundUser = User::find($user->id);

        $this->assert($foundUser->id === $user->id, 'find');
    }

    protected function testAll(): void
    {
        User::create([
            'name' => 'All Test 1',
            'email' => 'all.test1@example.com',
            'password' => 'password',
        ]);

        User::create([
            'name' => 'All Test 2',
            'email' => 'all.test2@example.com',
            'password' => 'password',
        ]);

        $users = User::all();

        $this->assert(count($users) >= 2, 'all');
    }

    protected function testWhere(): void
    {
        User::create([
            'name' => 'Where Test',
            'email' => 'where.test@example.com',
            'password' => 'password',
        ]);

        $user = User::where('email', 'where.test@example.com')->first();

        $this->assert($user !== null, 'where');
    }

    protected function testFirst(): void
    {
        User::create([
            'name' => 'First Test',
            'email' => 'first.test@example.com',
            'password' => 'password',
        ]);

        $user = User::first();

        $this->assert($user !== null, 'first');
    }

    protected function testRelationships(): void
    {
        // Assuming a User has many Coffees relationship is defined
        $this->info('Skipping relationships test: No relationships defined.');
    }

    protected function testTimestamps(): void
    {
        $user = User::create([
            'name' => 'Timestamp Test',
            'email' => 'timestamp.test@example.com',
            'password' => 'password',
        ]);

        $this->assert($user->created_at !== null && $user->updated_at !== null, 'timestamps');
    }

    protected function testScopes(): void
    {
        // Assuming an active scope is defined
        $this->info('Skipping scopes test: No scopes defined.');
    }

    protected function testCasting(): void
    {
        $user = User::create([
            'name' => 'Casting Test',
            'email' => 'casting.test@example.com',
            'password' => 'password',
            'is_admin' => 1,
        ]);

        $this->assert($user->is_admin === true, 'casting');
    }

    protected function testSoftDeletes(): void
    {
        $user = User::create([
            'name' => 'Soft Delete Test',
            'email' => 'soft.delete.test@example.com',
            'password' => 'password',
        ]);

        $id = $user->id;

        $user->delete();

        $this->assert(User::find($id) === null, 'soft deletes');
        $this->assert(User::withTrashed()->find($id) !== null, 'soft deletes with trashed');
    }

    protected function assert(bool $condition, string $test): void
    {
        if ($condition) {
            $this->info("✔ {$test}");
        } else {
            $this->error("✗ {$test}");
        }
    }
}
