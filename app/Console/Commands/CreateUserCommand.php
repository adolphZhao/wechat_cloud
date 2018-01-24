<?php
namespace App\Console\Commands;

use App\Repositories\LoginRepository;
use \Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    protected $signature = 'make:user {username} {password} {email}';

    protected $repository;

    public function __construct(LoginRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function handle()
    {
        $username = $this->argument('username');
        $password = $this->argument('password');
        $email = $this->argument('email');

        $user = $this->repository->createUser($username, $password, $email);

        $this->info(json_encode($user));
    }
}