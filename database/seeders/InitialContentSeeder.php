<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InitialContentSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = [
            ['name' => 'Micro Computer Institute', 'website_url' => 'https://mciedu.com', 'short_description' => 'Computer education, skills and training programs.', 'display_order' => 1],
            ['name' => 'C-Net Computer Institute', 'website_url' => 'https://cnetcomputer.mciedu.com', 'short_description' => 'Computer education and career-oriented learning.', 'display_order' => 2],
            ['name' => 'C-Net Pathshala', 'website_url' => 'https://c-net.mciedu.in', 'short_description' => 'Digital and academic learning platform for students.', 'display_order' => 3],
            ['name' => 'C-Net Library', 'website_url' => 'https://cnetlibrary.mciedu.com', 'short_description' => 'Library, study and knowledge services for learners.', 'display_order' => 4],
            ['name' => 'C-Net Web Services', 'website_url' => 'https://web.mciedu.in', 'short_description' => 'Website, digital presence and related web services.', 'display_order' => 5],
        ];

        foreach ($institutions as $item) {
            Institution::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                $item + ['is_active' => true]
            );
        }

        $settings = [
            'site_name' => 'MCI Educational Group',
            'tagline' => 'An Institution With Global Reach',
            'trust_name' => 'Chandrashekhar & Narayan Educational Trust',
            'phone_primary' => '7004773247',
            'phone_secondary' => '9334779133',
            'email' => 'mcieducationalgroup@gmail.com',
            'address' => 'MCI CAMPUS, Quamruddin Ganj, Bihar Sharif, Nalanda - 803101, Bihar, India',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
