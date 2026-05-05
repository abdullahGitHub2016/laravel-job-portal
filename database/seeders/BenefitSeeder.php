<?php
namespace Database\Seeders;

use App\Models\Benefit;
use Illuminate\Database\Seeder;

class BenefitSeeder extends Seeder
{
    public function run(): void
    {
        $benefits = [
            // Finance
            ['name' => 'Festival Bonus (2)',        'icon' => '🎁', 'category' => 'Finance',   'sort_order' => 1],
            ['name' => 'Performance Bonus',          'icon' => '💰', 'category' => 'Finance',   'sort_order' => 2],
            ['name' => 'Provident Fund',             'icon' => '🏦', 'category' => 'Finance',   'sort_order' => 3],
            ['name' => 'Gratuity',                   'icon' => '💵', 'category' => 'Finance',   'sort_order' => 4],
            ['name' => 'Annual Salary Increment',    'icon' => '📈', 'category' => 'Finance',   'sort_order' => 5],
            ['name' => 'Sales Commission',           'icon' => '💹', 'category' => 'Finance',   'sort_order' => 6],

            // Health
            ['name' => 'Health Insurance',           'icon' => '🏥', 'category' => 'Health',    'sort_order' => 10],
            ['name' => 'Life Insurance',             'icon' => '🛡️', 'category' => 'Health',    'sort_order' => 11],
            ['name' => 'Group Insurance',            'icon' => '👨‍👩‍👧', 'category' => 'Health',    'sort_order' => 12],
            ['name' => 'Medical Allowance',          'icon' => '💊', 'category' => 'Health',    'sort_order' => 13],

            // Leave
            ['name' => 'Earned Leave',               'icon' => '🌴', 'category' => 'Leave',     'sort_order' => 20],
            ['name' => 'Casual Leave',               'icon' => '☀️', 'category' => 'Leave',     'sort_order' => 21],
            ['name' => 'Sick Leave',                 'icon' => '🤒', 'category' => 'Leave',     'sort_order' => 22],
            ['name' => 'Maternity Leave',            'icon' => '👶', 'category' => 'Leave',     'sort_order' => 23],
            ['name' => 'Paternity Leave',            'icon' => '👨‍🍼', 'category' => 'Leave',     'sort_order' => 24],

            // Work
            ['name' => 'Work From Home',             'icon' => '🏠', 'category' => 'Work',      'sort_order' => 30],
            ['name' => 'Flexible Hours',             'icon' => '⏰', 'category' => 'Work',      'sort_order' => 31],
            ['name' => 'Weekly 2 Holidays',          'icon' => '📅', 'category' => 'Work',      'sort_order' => 32],
            ['name' => 'Half Day on Friday',         'icon' => '🕐', 'category' => 'Work',      'sort_order' => 33],

            // Perks
            ['name' => 'Lunch Facility',             'icon' => '🍱', 'category' => 'Perks',     'sort_order' => 40],
            ['name' => 'Transport Facility',         'icon' => '🚌', 'category' => 'Perks',     'sort_order' => 41],
            ['name' => 'Mobile Allowance',           'icon' => '📱', 'category' => 'Perks',     'sort_order' => 42],
            ['name' => 'TA / DA',                    'icon' => '✈️', 'category' => 'Perks',     'sort_order' => 43],
            ['name' => 'Training & Development',     'icon' => '📚', 'category' => 'Perks',     'sort_order' => 44],
            ['name' => 'Laptop Provided',            'icon' => '💻', 'category' => 'Perks',     'sort_order' => 45],
        ];

        foreach ($benefits as $b) {
            Benefit::create($b);
        }
    }
}
