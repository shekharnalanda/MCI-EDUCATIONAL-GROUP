<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $updates = [
            [
                'slug' => 'mci-ecosystem-directory-expanded',
                'title' => 'MCI Ecosystem Directory: Institutions & Digital Projects',
                'excerpt' => 'MCI Educational Group के institutions, services और digital projects अब एक ही homepage directory में उपलब्ध हैं।',
                'content' => 'The MCI Educational Group portal now presents its education institutions, learner services, business platforms and digital projects through one connected directory. Visitors can directly explore the relevant website or service from the homepage.',
                'image' => '/images/news/mci-ecosystem-update.svg',
                'published_at' => '2026-09-16 19:30:00',
            ],
            [
                'slug' => 'mci-test-series-kyp-learning-update',
                'title' => 'MCI Test Series & KYP: Learning and Skill Update',
                'excerpt' => 'Bilingual test practice, computer knowledge, communication और practical digital skills पर केंद्रित learning services।',
                'content' => 'MCI Test Series supports structured bilingual practice and assessment, while the Kushal Youth Program focuses on computer knowledge, communication, soft skills and practical digital learning for young learners.',
                'image' => '/images/news/mci-learning-update.svg',
                'published_at' => '2026-09-16 19:20:00',
            ],
            [
                'slug' => 'salary-book-vyapar-practical-business-tools',
                'title' => 'Salary Book & C-Net Vyapar: Practical Business Tools',
                'excerpt' => 'Attendance, salary, billing, inventory और daily business management को आसान बनाने वाले practical platforms।',
                'content' => 'Salary Book brings GPS and selfie attendance, payroll sheets and staff records together. C-Net Vyapar supports billing, inventory, accounting and customer-management workflows for growing businesses.',
                'image' => '/images/news/mci-digital-services-update.svg',
                'published_at' => '2026-09-16 19:10:00',
            ],
            [
                'slug' => 'mci-search-engine-local-business-discovery',
                'title' => 'MCI Search Engine: Jo Chahiye, Aas-Paas Search Kijiye',
                'excerpt' => 'Local businesses और nearby services को category, location और verified profile के माध्यम से खोजने की सुविधा।',
                'content' => 'MCI Search Engine is designed for local discovery, helping users find nearby businesses and services while giving business owners a professional digital profile and enquiry channel.',
                'image' => '/images/news/mci-digital-services-update.svg',
                'published_at' => '2026-09-16 19:00:00',
            ],
            [
                'slug' => 'cnet-meet-online-collaboration-update',
                'title' => 'C-Net Meet: Secure Online Collaboration',
                'excerpt' => 'Online classes और meetings के लिए invite sharing, waiting room तथा host controls जैसी उपयोगी सुविधाएँ।',
                'content' => 'C-Net Meet provides a self-hosted collaboration experience with invitation sharing, waiting-room control and host tools for classes, institutional discussions and group meetings.',
                'image' => '/images/news/mci-digital-services-update.svg',
                'published_at' => '2026-09-16 18:50:00',
            ],
            [
                'slug' => 'library-pathshala-computer-education-services',
                'title' => 'Library, Pathshala & Computer Education Services',
                'excerpt' => 'Study support, school learning और career-oriented computer education को एक connected education network में जोड़ा गया है।',
                'content' => 'C-Net Library supports focused study and access to learning resources. C-Net Pathshala serves school learners, while C-Net Computer Education offers practical, career-oriented computer courses and training.',
                'image' => '/images/news/mci-learning-update.svg',
                'published_at' => '2026-09-16 18:40:00',
            ],
        ];

        foreach ($updates as $update) {
            DB::table('news_posts')->updateOrInsert(
                ['slug' => $update['slug']],
                $update + ['is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        // Keep published content during rollback so admin-managed news is never lost.
    }
};
