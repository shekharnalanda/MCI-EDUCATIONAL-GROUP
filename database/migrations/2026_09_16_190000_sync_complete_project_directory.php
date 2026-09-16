<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $projects = [
            ['MCI Educational Group', 'mci-educational-group', 'https://mciedu.in', 'Official umbrella portal for education, skills, technology and group services.', '/images/mci-logo.png'],
            ['MCI Search Engine', 'mci-search-engine', 'https://esearch.mciedu.com', 'Local business and service search platform for nearby verified listings.'],
            ['MCI Test Series', 'mci-test-series', 'https://test.mciedu.com', 'Bilingual online tests, practice, results and competitive-exam preparation.'],
            ['C-Net Meet', 'c-net-meet', 'https://meet.mciedu.com', 'Self-hosted online meeting, classroom and collaboration platform.', null, ['c-net-audio-meet']],
            ['C-Net Social Media', 'c-net-social-media', 'https://social.mciedu.com', 'Private social network for the MCI community, institutions and learners.'],
            ['C-Net AI Studio', 'c-net-ai-studio', 'https://studio.mciedu.com', 'AI-powered creative tools, automation and digital production workspace.'],
            ['C-Net Web Services', 'c-net-web-services', 'https://web.mciedu.com', 'Website development, hosting support and digital business solutions.'],
            ['C-Net Store', 'c-net-store', 'https://cnetstore.mciedu.com', 'Online store for computers, accessories, learning material and services.'],
            ['Shekhar Family Portal', 'shekhar-family-portal', 'https://family.mciedu.in', 'Private family information, memories and relationship portal.', null, ['shekhar-family']],
            ['C-Net Library', 'c-net-library', 'https://cnetlibrary.mciedu.com', 'Library membership, study resources and learner services.'],
            ['C-Net Pathshala', 'c-net-pathshala', 'https://cnet.mciedu.in', 'School education and digital academic learning for students.'],
            ['C-Net Computer Education', 'c-net-computer-education', 'https://cnetcomputer.mciedu.com', 'Career-focused computer education, courses and practical training.', null, ['c-net-computer-institute']],
            ['C-Net PagarBOOK', 'c-net-pagarbook', 'https://pgarbook.mciedu.com', 'Staff attendance, payroll and workforce management solution.'],
            ['Book My Event', 'book-my-event', 'https://bookmyevent.mciedu.com', 'Event discovery, booking, ticketing and secure gate-scanning platform.'],
            ['Kushal Youth Program (KYP)', 'kushal-youth-program-kyp', 'https://kyp.mciedu.com', 'CIT, communication, soft-skills and AI-enabled youth training.', null, ['kushal-youth-programme', 'kushal-youth-program']],
            ['Salary Book', 'salary-book', 'https://salarybook.mciedu.com', 'GPS and selfie attendance with salary sheets, payslips and staff records.'],
            ['C-Net Vyapar', 'c-net-vyapar', 'https://vyapar.mciedu.in', 'Business billing, inventory, accounting and customer-management suite.'],
            ['Aryans Dandiya Night 2.0', 'aryans-dandiya-night-2-0', 'https://dandiyanight.mciedu.in', 'Free event registration, wristbands, QR tickets and secure check-in.'],
            ['C-Net AI Work', 'c-net-ai-work', null, 'Private Windows desktop AI assistant for local chat and development workflows.'],
            ['C-Net Web Services Starter', 'c-net-web-services-starter', null, 'Reusable starter template for secure and consistent client websites.'],
            ['Priya Rani Trial Website', 'priya-rani-trial-website', 'https://priya.mciedu.com', 'Trial website created through the C-Net Web Services workflow.'],
            ['MCI Biometric Connector', 'mci-biometric-connector', null, 'Windows connector for secure local biometric attendance devices.'],
            ['KYP Iris Connector V2/V3/V4', 'kyp-iris-connector-v2-v3-v4', null, 'Windows connector for Mantra MIS100V2 iris attendance integration.'],
        ];

        DB::transaction(function () use ($projects): void {
            foreach ($projects as $index => $project) {
                [$name, $slug, $website, $description] = $project;
                $logo = $project[4] ?? null;
                $aliases = $project[5] ?? [];
                $query = DB::table('institutions')->where('slug', $slug);

                if ($aliases !== []) {
                    $query->orWhereIn('slug', $aliases);
                }

                $existing = $query->orderBy('id')->first();
                $values = [
                    'name' => $name,
                    'website_url' => $website,
                    'short_description' => $description,
                    'description' => $description,
                    'display_order' => $index + 1,
                    'is_active' => true,
                    'updated_at' => now(),
                ];

                if ($existing) {
                    DB::table('institutions')->where('id', $existing->id)->update($values);
                    continue;
                }

                DB::table('institutions')->insert($values + [
                    'slug' => $slug,
                    'logo' => $logo,
                    'created_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        // Directory records are intentionally retained to avoid deleting admin-managed data.
    }
};
