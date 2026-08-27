<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceItem;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Users ──────────────────────────────────────────
        User::updateOrCreate(['email' => 'dimas@npc.com'], [
            'name' => 'NPC Administrator Dimas', 'password' => 'dimasdimas', 'role' => 'admin',
        ]);
        User::updateOrCreate(['email' => 'baji@npc.com'], [
            'name' => 'NPC User Bhazy', 'password' => 'bajibaji', 'role' => 'user',
        ]);

        // ── Tags ───────────────────────────────────────────
        $tagNames = [
            'Education', 'Educational Event', 'School', 'University', 'Student',
            'Workshop', 'Seminar', 'Competition', 'Youth', 'Community',
            'Academic', 'Youth Program', 'Branding', 'Digital', 'Web',
            'UI/UX', 'Motion', 'Content', 'Production', 'Campaign',
            'Event', 'Corporate', 'Identity', 'Print', 'Social Media',
            'Animation', 'Photography', 'Video',
        ];
        foreach ($tagNames as $name) {
            Tag::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        // ── Services ───────────────────────────────────────
        $services = [
            [
                'title'             => 'Branding & Identity',
                'slug'              => 'branding',
                'sort_order'        => 1,
                'is_published'      => true,
                'short_description' => 'Membangun identitas visual yang kuat, konsisten, dan berkarakter untuk brand, organisasi, maupun event.',
                'description'       => "Identitas merek yang kuat berakar dari pemahaman mendalam terhadap nilai, visi, dan audiens. Kami merancang sistem visual yang kohesif — mulai dari logo hingga panduan merek lengkap — yang mencerminkan karakter unik setiap klien.\n\nPendekatan kami menggabungkan riset budaya, tipografi strategis, dan estetika yang relevan untuk menciptakan merek yang tidak hanya terlihat indah, tetapi juga komunikatif dan tahan waktu.",
                'items'             => ['Logo Design', 'Visual Identity', 'Brand Guidelines', 'Typography System', 'Visual System'],
                'tag_names'         => ['Branding', 'Identity', 'Corporate'],
            ],
            [
                'title'             => 'Graphic Design',
                'slug'              => 'graphic-design',
                'sort_order'        => 2,
                'is_published'      => true,
                'short_description' => 'Menerjemahkan ide menjadi visual yang komunikatif dan menarik untuk berbagai kebutuhan digital maupun cetak.',
                'description'       => "Desain grafis adalah bahasa visual yang menjembatani pesan dengan audiens. Kami mengerjakan berbagai kebutuhan visual — dari poster event, materi cetak, hingga konten digital — dengan mempertahankan konsistensi estetika dan kejelasan pesan.",
                'items'             => ['Poster & Banner', 'Placard', 'Print Design', 'Social Media', 'Illustration'],
                'tag_names'         => ['Branding', 'Print', 'Digital'],
            ],
            [
                'title'             => 'Event & Publication',
                'slug'              => 'event-publication',
                'sort_order'        => 3,
                'is_published'      => true,
                'short_description' => 'Menciptakan kebutuhan visual yang menyeluruh untuk event, konferensi, organisasi, dan berbagai materi publikasi.',
                'description'       => "Event yang berkesan dimulai dari identitas visual yang kuat. Kami menyediakan solusi visual menyeluruh untuk event — mulai dari konferensi pendidikan, seminar, kompetisi, hingga festival kreatif — termasuk semua materi publikasi yang diperlukan.",
                'items'             => ['Event Materials', 'Certificate', 'Booklet & Guide', 'ID Card', 'Signage'],
                'tag_names'         => ['Event', 'Educational Event', 'Print', 'Branding'],
            ],
            [
                'title'             => 'Social Media',
                'slug'              => 'social-media',
                'sort_order'        => 4,
                'is_published'      => true,
                'short_description' => 'Menciptakan konten visual yang konsisten, menarik, dan relevan untuk membangun komunikasi dengan audiens.',
                'description'       => "Kehadiran digital yang konsisten dan autentik membutuhkan strategi konten yang terencana. Kami merancang konten sosial media yang tidak hanya estetis, tetapi juga strategis dalam membangun engagement dan brand awareness.",
                'items'             => ['Social Posts', 'Carousel', 'Stories', 'Campaign Visuals', 'Content Design'],
                'tag_names'         => ['Social Media', 'Campaign', 'Digital', 'Content'],
            ],
            [
                'title'             => 'Presentation & Document',
                'slug'              => 'presentation-document',
                'sort_order'        => 5,
                'is_published'      => true,
                'short_description' => 'Mengubah informasi menjadi materi visual yang profesional, terstruktur, dan mudah dipahami.',
                'description'       => "Presentasi yang baik adalah investasi dalam komunikasi profesional. Kami merancang deck presentasi, proposal, company profile, dan laporan yang tidak hanya indah secara visual, tetapi juga efektif dalam menyampaikan pesan.",
                'items'             => ['Presentation Deck', 'Proposal Design', 'Company Profile', 'Report Design', 'Editorial Layout'],
                'tag_names'         => ['Corporate', 'Print', 'Digital'],
            ],
            [
                'title'             => 'Motion & Visual Content',
                'slug'              => 'motion-visual-content',
                'sort_order'        => 6,
                'is_published'      => true,
                'short_description' => 'Menghidupkan ide melalui animasi, motion graphics, dan konten visual yang menarik perhatian.',
                'description'       => "Motion graphics dan konten video adalah medium yang paling kuat dalam komunikasi digital. Kami memproduksi animasi, teaser event, logo motion, dan konten video yang dirancang untuk menarik perhatian dan menyampaikan pesan dengan dampak maksimal.",
                'items'             => ['Motion Graphics', 'Logo Animation', 'Event Teaser', 'Social Media Motion', 'Video Content'],
                'tag_names'         => ['Motion', 'Animation', 'Video', 'Content'],
            ],
        ];

        foreach ($services as $sd) {
            $itemTitles = $sd['items'];
            $tagNames   = $sd['tag_names'];
            unset($sd['items'], $sd['tag_names']);

            $service = Service::updateOrCreate(['slug' => $sd['slug']], $sd);

            // Service items
            if ($service->wasRecentlyCreated || $service->items()->count() === 0) {
                $service->items()->delete();
                foreach ($itemTitles as $i => $t) {
                    ServiceItem::updateOrCreate(
                        ['service_id' => $service->id, 'title' => $t],
                        ['sort_order' => $i]
                    );
                }
            }

            // Tags
            $tagIds = [];
            foreach ($tagNames as $n) {
                $tag = Tag::firstOrCreate(['slug' => Str::slug($n)], ['name' => $n]);
                $tagIds[] = $tag->id;
            }
            $service->tags()->syncWithoutDetaching($tagIds);
        }

        // ── Works (delegate to separate seeder) ────────────
        $this->call(WorksTableSeeder::class);
    }
}
