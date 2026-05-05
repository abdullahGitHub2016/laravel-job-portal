<?php
namespace Database\Seeders;

use App\Models\Benefit;
use App\Models\Category;
use App\Models\Industry;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BenefitSeeder::class);

        $categories = [
            'IT & Telecommunication', 'Banking & Finance', 'Marketing & Sales',
            'Engineering', 'Healthcare & Pharma', 'Education & Training',
            'Design & Creative', 'Customer Service', 'HR & Admin', 'Logistics & Supply Chain',
        ];

        foreach ($categories as $i => $name) {
            Category::create([
                'name'       => $name,
                'slug'       => Str::slug($name),
                'is_active'  => true,
                'sort_order' => $i,
            ]);
        }

        $industries = [
            'Technology', 'Banking', 'Healthcare', 'Education', 'Manufacturing',
            'Retail', 'Telecommunications', 'Media & Entertainment', 'NGO', 'Government',
        ];

        foreach ($industries as $name) {
            Industry::create(['name' => $name, 'slug' => Str::slug($name)]);
        }

        $skills = [
            ['name' => 'PHP',           'category' => 'Programming'],
            ['name' => 'Laravel',       'category' => 'Programming'],
            ['name' => 'Vue.js',        'category' => 'Programming'],
            ['name' => 'React',         'category' => 'Programming'],
            ['name' => 'JavaScript',    'category' => 'Programming'],
            ['name' => 'Python',        'category' => 'Programming'],
            ['name' => 'MySQL',         'category' => 'Database'],
            ['name' => 'PostgreSQL',    'category' => 'Database'],
            ['name' => 'Docker',        'category' => 'DevOps'],
            ['name' => 'AWS',           'category' => 'DevOps'],
            ['name' => 'Figma',         'category' => 'Design'],
            ['name' => 'Photoshop',     'category' => 'Design'],
            ['name' => 'SEO',           'category' => 'Marketing'],
            ['name' => 'Excel',         'category' => 'General'],
            ['name' => 'Communication', 'category' => 'General'],
        ];

        foreach ($skills as $skill) {
            Skill::create([
                'name'     => $skill['name'],
                'slug'     => Str::slug($skill['name']),
                'category' => $skill['category'],
            ]);
        }
    }
}
