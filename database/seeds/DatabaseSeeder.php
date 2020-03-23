<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Contact;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
      //create users 0 through 19
      for($i = 0;$i<20;$i++){
        $user = new User;
        $user->name = 'user'.$i;
        $user->email = $i.'@contact.com';
        $user->latitude = 1.11;
        $user->longitude = 2.22;
        $user->password = Hash::make('password');
        $user->save();
      }
      
      //put users 0 to 4 in the same residence
      for($i = 1; $i<5; $i++){
        $contact = new Contact;
        $contact->parent_id = 0;
        $contact->child_id = $i;
        $contact->same_residence = true;
        $contact->save();
      }

      //put users 5 to 9 in the same residence
      for($i = 6;$i<10;$i++){
        $contact = new Contact;
        $contact->parent_id = 5;
        $contact->child_id = $i;
        $contact->same_residence = true;
        $contact->save();
      }

      for($i=0;$i<5;$i++){
        $date = Carbon::now();
        $date = $date->subDays($i * 4);
        $contact = new Contact;
        $contact->parent_id = 1;
        $contact->child_id = $i + 10;
        $contact->same_residence = false;
        $contact->created_at = $date;
        $contact->updated_at = $date;
        $contact->save();
      }
    }
}
