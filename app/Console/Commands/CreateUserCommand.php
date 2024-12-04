<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use DB;
use Hash;
use Illuminate\Console\Command;
use Illuminate\Validation\Rules\Password;

use Validator;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user['name']= $this->ask('Name of the new user');
        $user['email']= $this->ask('Email of the new user');
        $user['password']= $this->secret('Password of the new user');

        $rolename=$this->choice('Role of the new user',['admin','editor'],1);
        $role=Role::where('name',$rolename)->first();
        if(! $role){
            $this->error('Role Not Found');
            return -1;
        }

        $validator=Validator::make($user,[
            'name'=>['required','string','max:255'],
            'email'=>['required','string','email','max:255','unique:'. User::class],
            'password'=>['required',Password::defaults()]
        ]);

        if($validator->fails()){
            foreach($validator->errors()->all() as $error){
                $this->error($error);
            }
            return -1;
        }

        DB::transaction(function() use ($user,$role){
            $user['password']=Hash::make($user['password']);
            $newuser=User::create($user);
            $newuser->roles()->attach($role->id);
        });
        
        $this->info('User '. $user['email']. ' created successfully');
        return 0;
    }
}
