<?php

use Carbon\Carbon;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserTableSeeder');
        $this->call('JournalTableSeeder');
	}
}

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->truncate();

        $faker = Faker\Factory::create();

        for ($i = 0; $i < 10; $i++)
        {
            $user = new User();

            $user->username = 'testaccount' . $i;
            $user->password = Hash::make('P@ssw0rd');
            $user->email = $faker->email();
            $user->first_name = $faker->firstName();
            $user->last_name = $faker->lastName();
            $user->last_login = Carbon::now()->format('Y-m-d H:i:s');

            $user->save();
        }

    }

}

class JournalTableSeeder extends Seeder {

    public function run()
    {
        DB::table('journals')->truncate();

        $faker = Faker\Factory::create();

        foreach (range(1, 50) as $index)
        {
            $journal = new Journal();

            $journal->user_id = $faker->randomDigit();
            $journal->publish_date = Carbon::instance($faker->dateTimeBetween($startDate = Config::get('constants.ANNIVERSARY')));
            $journal->volume = $journal->publish_date->diffInMonths(Config::get('constants.ANNIVERSARY')) + 2;
            $journal->day = $journal->publish_date->diffInDays(Config::get('constants.ANNIVERSARY')) + 1;
            $journal->contents = implode("\n\n", $faker->paragraphs(5));
            $journal->special_events = implode("\n", $faker->sentences(2));

            $journal->save();
        }

    }

}