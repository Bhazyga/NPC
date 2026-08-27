<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceItem;
use App\Models\Tag;
use App\Models\User;
use App\Models\Work;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Users ──────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'dimas@npc.com'],
            ['name' => 'NPC Administrator Dimas', 'password' => 'dimasdimas', 'role' => 'admin']
        );
        User::updateOrCreate(
            ['email' => 'baji@npc.com'],
            ['name' => 'NPC User Bhazy', 'password' => 'bajibaji', 'role' => 'user']
        );

        // ── Tags (education-first, then creative) ──────────
        $tagData = [
            // Education taxonomy
            'Education', 'Educational Event', 'School', 'University',
            'Student', 'Workshop', 'Seminar', 'Competition', 'Youth',
            'Community', 'Academic', 'Youth Program',
            // Creative taxonomy
            'Branding', 'Digital', 'Web', 'UI/UX', 'Motion',
            'Content', 'Production', 'Campaign', 'Event',
            'Corporate', 'Identity', 'Print', 'Social Media',
            'Animation', 'Photography', 'Video',
        ];

        $tags = [];
        foreach ($tagData as $name) {
            $tag = Tag::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
            $tags[$name] = $tag->id;
        }

        // ── Services ───────────────────────────────────────
        $servicesData = [
            [
                'title'             => 'Branding & Identity',
                'slug'              => 'branding',
                'sort_order'        => 1,
                'short_description' => 'Membangun identitas visual yang kuat, konsisten, dan berkarakter untuk brand, organisasi, maupun event.',
                'description'       => "Identitas merek yang kuat berakar dari pemahaman mendalam terhadap nilai, visi, dan audiens. Kami merancang sistem visual yang kohesif — mulai dari logo hingga panduan merek lengkap — yang mencerminkan karakter unik setiap klien.\n\nPendekatan kami menggabungkan riset budaya, tipografi strategis, dan estetika yang relevan untuk menciptakan merek yang tidak hanya terlihat indah, tetapi juga komunikatif dan tahan waktu.",
                'is_published'      => true,
                'items'             => ['Logo Design', 'Visual Identity', 'Brand Guidelines', 'Typography System', 'Visual System'],
                'tag_names'         => ['Branding', 'Identity', 'Corporate'],
            ],
            [
                'title'             => 'Graphic Design',
                'slug'              => 'graphic-design',
                'sort_order'        => 2,
                'short_description' => 'Menerjemahkan ide menjadi visual yang komunikatif dan menarik untuk berbagai kebutuhan digital maupun cetak.',
                'description'       => "Desain grafis adalah bahasa visual yang menjembatani pesan dengan audiens. Kami mengerjakan berbagai kebutuhan visual — dari poster event, materi cetak, hingga konten digital — dengan mempertahankan konsistensi estetika dan kejelasan pesan.",
                'is_published'      => true,
                'items'             => ['Poster & Banner', 'Placard', 'Print Design', 'Social Media', 'Illustration'],
                'tag_names'         => ['Branding', 'Print', 'Digital'],
            ],
            [
                'title'             => 'Event & Publication',
                'slug'              => 'event-publication',
                'sort_order'        => 3,
                'short_description' => 'Menciptakan kebutuhan visual yang menyeluruh untuk event, konferensi, organisasi, dan berbagai materi publikasi.',
                'description'       => "Event yang berkesan dimulai dari identitas visual yang kuat. Kami menyediakan solusi visual menyeluruh untuk event — mulai dari konferensi pendidikan, seminar, kompetisi, hingga festival kreatif — termasuk semua materi publikasi yang diperlukan.",
                'is_published'      => true,
                'items'             => ['Event Materials', 'Certificate', 'Booklet & Guide', 'ID Card', 'Signage'],
                'tag_names'         => ['Event', 'Educational Event', 'Print', 'Branding'],
            ],
            [
                'title'             => 'Social Media',
                'slug'              => 'social-media',
                'sort_order'        => 4,
                'short_description' => 'Menciptakan konten visual yang konsisten, menarik, dan relevan untuk membangun komunikasi dengan audiens.',
                'description'       => "Kehadiran digital yang konsisten dan autentik membutuhkan strategi konten yang terencana. Kami merancang konten sosial media yang tidak hanya estetis, tetapi juga strategis dalam membangun engagement dan brand awareness.",
                'is_published'      => true,
                'items'             => ['Social Posts', 'Carousel', 'Stories', 'Campaign Visuals', 'Content Design'],
                'tag_names'         => ['Social Media', 'Campaign', 'Digital', 'Content'],
            ],
            [
                'title'             => 'Presentation & Document',
                'slug'              => 'presentation-document',
                'sort_order'        => 5,
                'short_description' => 'Mengubah informasi menjadi materi visual yang profesional, terstruktur, dan mudah dipahami.',
                'description'       => "Presentasi yang baik adalah investasi dalam komunikasi profesional. Kami merancang deck presentasi, proposal, company profile, dan laporan yang tidak hanya indah secara visual, tetapi juga efektif dalam menyampaikan pesan.",
                'is_published'      => true,
                'items'             => ['Presentation Deck', 'Proposal Design', 'Company Profile', 'Report Design', 'Editorial Layout'],
                'tag_names'         => ['Corporate', 'Print', 'Digital'],
            ],
            [
                'title'             => 'Motion & Visual Content',
                'slug'              => 'motion-visual-content',
                'sort_order'        => 6,
                'short_description' => 'Menghidupkan ide melalui animasi, motion graphics, dan konten visual yang menarik perhatian.',
                'description'       => "Motion graphics dan konten video adalah medium yang paling kuat dalam komunikasi digital. Kami memproduksi animasi, teaser event, logo motion, dan konten video yang dirancang untuk menarik perhatian dan menyampaikan pesan dengan dampak maksimal.",
                'is_published'      => true,
                'items'             => ['Motion Graphics', 'Logo Animation', 'Event Teaser', 'Social Media Motion', 'Video Content'],
                'tag_names'         => ['Motion', 'Animation', 'Video', 'Content'],
            ],
        ];

        foreach ($servicesData as $serviceData) {
            $itemTitles = $serviceData['items'];
            $tagNames   = $serviceData['tag_names'];
            unset($serviceData['items'], $serviceData['tag_names']);

            $service = Service::updateOrCreate(
                ['slug' => $serviceData['slug']],
                $serviceData
            );

            // Service items
            if ($service->wasRecentlyCreated || $service->items()->count() === 0) {
                $service->items()->delete();
                foreach ($itemTitles as $i => $itemTitle) {
                    ServiceItem::updateOrCreate(
                        ['service_id' => $service->id, 'title' => $itemTitle],
                        ['sort_order' => $i]
                    );
                }
            }

            // Tags
            $tagIds = array_filter(array_map(fn ($n) => $tags[$n] ?? null, $tagNames));
            $service->tags()->syncWithoutDetaching($tagIds);
        }

        // ── Works: backfill slugs and publish existing works ──
        $workSeedData = [
            [
                'title'             => 'Global Millennial Model United Nations 22nd',
                'slug'              => 'global-millennial-model-united-nations-22nd',
                'category'          => 'Event & Publication',
                'year'              => '2026',
                'short_description' => 'Desain identitas visual, sistem penerbitan digital, dan produksi branding panggung untuk konferensi diplomasi internasional pemuda.',
                'description'       => 'Proyek ini mencakup perancangan identitas visual menyeluruh untuk GMMUN ke-22, sebuah konferensi diplomasi internasional yang diikuti ratusan delegasi muda. Kami mengembangkan sistem visual yang mencerminkan semangat diplomasi, keragaman budaya, dan kepemimpinan generasi muda.',
                'client'            => 'GMMUN Committee',
                'color'             => '#FD0000',
                'is_published'      => true,
                'is_featured'       => true,
                'sort_order'        => 1,
                'tag_names'         => ['Event', 'Educational Event', 'Youth', 'Branding'],
            ],
            [
                'title'             => 'Nusantara Soundscape Festival 2025',
                'slug'              => 'nusantara-soundscape-festival-2025',
                'category'          => 'Event & Publication',
                'year'              => '2025',
                'short_description' => 'Produksi tata panggung, animasi motion grafik LED, serta campaign sosial media untuk pertunjukan musik lintas budaya.',
                'description'       => 'Festival musik yang merayakan kekayaan soundscape Nusantara ini membutuhkan identitas visual yang kuat dan sistem produksi konten yang konsisten. Kami merancang konsep visual panggung, animasi LED, materi sosial media, dan kampanye promosi digital.',
                'client'            => 'Nusantara Arts Collective',
                'color'             => '#C7983F',
                'is_published'      => true,
                'is_featured'       => true,
                'sort_order'        => 2,
                'tag_names'         => ['Event', 'Motion', 'Campaign', 'Branding'],
            ],
            [
                'title'             => 'Karsa Identity System & Brand Activation',
                'slug'              => 'karsa-identity-system-brand-activation',
                'category'          => 'Corporate & Brand Activation',
                'year'              => '2025',
                'short_description' => 'Perancangan identitas visual merek dan aktivitas peluncuran produk berbasis arsitektur pengalaman interaktif.',
                'description'       => 'Karsa adalah merek baru yang membutuhkan identitas visual yang kuat dan sistem aktivasi merek yang kohesif. Kami merancang keseluruhan sistem identitas — dari logo, panduan merek, hingga materi peluncuran produk — yang mencerminkan nilai-nilai merek yang modern dan berkarakter.',
                'client'            => 'Karsa Brand',
                'color'             => '#3B82F6',
                'is_published'      => true,
                'is_featured'       => false,
                'sort_order'        => 3,
                'tag_names'         => ['Branding', 'Corporate', 'Identity'],
            ],
        ];

        foreach ($workSeedData as $workData) {
            $tagNames = $workData['tag_names'];
            unset($workData['tag_names']);

            $work = Work::updateOrCreate(
                ['title' => $workData['title']],
                $workData
            );

            $tagIds = array_filter(array_map(fn ($n) => $tags[$n] ?? null, $tagNames));
            $work->normalizedTags()->syncWithoutDetaching($tagIds);
        }
    }
}
