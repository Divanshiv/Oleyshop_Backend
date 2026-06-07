<?php

namespace Database\Seeders;

use App\Model\MatrixLevel;
use Illuminate\Database\Seeder;

class MatrixLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['level' => 1,  'position_name' => 'Bronze',     'required_members' => 4,         'incentive_amount' => 1100],
            ['level' => 2,  'position_name' => 'Silver',     'required_members' => 16,        'incentive_amount' => 2500],
            ['level' => 3,  'position_name' => 'Gold',       'required_members' => 64,        'incentive_amount' => 11000],
            ['level' => 4,  'position_name' => 'Pearl',      'required_members' => 256,       'incentive_amount' => 25000],
            ['level' => 5,  'position_name' => 'Ruby',       'required_members' => 1024,      'incentive_amount' => 51000],
            ['level' => 6,  'position_name' => 'Star',       'required_members' => 4096,      'incentive_amount' => 100000],
            ['level' => 7,  'position_name' => 'Emerald',    'required_members' => 16384,     'incentive_amount' => 251000],
            ['level' => 8,  'position_name' => 'Platin',     'required_members' => 65536,     'incentive_amount' => 500000],
            ['level' => 9,  'position_name' => 'Venus',      'required_members' => 262144,    'incentive_amount' => 1100000],
            ['level' => 10, 'position_name' => 'Ambassador', 'required_members' => 1048576,   'incentive_amount' => 2500000],
            ['level' => 11, 'position_name' => 'Diamond',    'required_members' => 4194304,   'incentive_amount' => 5100000],
            ['level' => 12, 'position_name' => 'King',       'required_members' => 16777216,  'incentive_amount' => 12100000],
        ];

        foreach ($levels as $level) {
            MatrixLevel::updateOrCreate(
                ['level' => $level['level']],
                $level
            );
        }
    }
}
