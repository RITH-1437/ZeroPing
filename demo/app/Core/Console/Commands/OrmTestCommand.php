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
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'username'   => 'johndoe_create',
            'email'      => 'john.doe.create@example.com',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $this->assert($user->id !== null, 'create');
    }

    protected function testSave(): void
    {
        $user = new User([
            'first_name' => 'Jane',
            'last_name'  => 'Doe',
            'username'   => 'janedoe_save',
            'email'      => 'jane.doe.save@example.com',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $user->save();

        $this->assert($user->id !== null, 'save');
    }

    protected function testUpdate(): void
    {
        $user = User::create([
            'first_name' => 'Update',
            'last_name'  => 'Test',
            'username'   => 'updatetest',
            'email'      => 'update.test@example.com',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $user->update([
            'first_name' => 'Updated',
        ]);

        $this->assert($user->first_name === 'Updated', 'update');
    }

    protected function testDelete(): void
    {
        $user = User::create([
            'first_name' => 'Delete',
            'last_name'  => 'Test',
            'username'   => 'deletetest',
            'email'      => 'delete.test@example.com',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $id = $user->id;

        $user->delete();

        $this->assert(User::find($id) === null, 'delete');
    }

    protected function testFind(): void
    {
        $user = User::create([
            'first_name' => 'Find',
            'last_name'  => 'Test',
            'username'   => 'findtest',
            'email'      => 'find.test@example.com',
            'password'   => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $foundUser = User::find($user->id);

        $this->assert($foundUser->id === $user->id, 'find');
    }

    protected function testAll(): void
    {
        User::create([
            'first_name' => 'All1', 'last_name' => 'Test', 'username' => 'alltest1',
            'email' => 'all.test1@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT),
        ]);
        User::create([
            'first_name' => 'All2', 'last_name' => 'Test', 'username' => 'alltest2',
            'email' => 'all.test2@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $users = User::all();

        $this->assert(count($users) >= 2, 'all');
    }

    protected function testWhere(): void
    {
        User::create([
            'first_name' => 'Where', 'last_name' => 'Test', 'username' => 'wheretest',
            'email' => 'where.test@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $user = User::where('email', 'where.test@example.com')->first();

        $this->assert($user !== null, 'where');
    }

    protected function testFirst(): void
    {
        User::create([
            'first_name' => 'First', 'last_name' => 'Test', 'username' => 'firsttest',
            'email' => 'first.test@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $user = User::first();

        $this->assert($user !== null, 'first');
    }

    protected function testRelationships(): void
    {
        $this->info('Skipping relationships test: No relationships defined.');
    }

    protected function testTimestamps(): void
    {
        $user = User::create([
            'first_name' => 'Timestamp', 'last_name' => 'Test', 'username' => 'tstest',
            'email' => 'timestamp.test@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $this->assert($user->created_at !== null && $user->updated_at !== null, 'timestamps');
    }

    protected function testScopes(): void
    {
        $this->info('Skipping scopes test: No scopes defined.');
    }

    protected function testCasting(): void
    {
        $this->info('Skipping casting test: No cast columns in users table.');
    }

    protected function testSoftDeletes(): void
    {
        $user = User::create([
            'first_name' => 'Soft', 'last_name' => 'Delete', 'username' => 'softdel',
            'email' => 'soft.delete.test@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $id = $user->id;
        $user->delete();

        $this->assert(User::find($id) === null, 'soft deletes (deleted_at set)');
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
