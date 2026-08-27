<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Work;
use App\Models\WorkMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorksTableSeeder extends Seeder
{
    public function run(): void
    {
        $works = [
            [
                'num'               => '01',
                'title'             => 'Global Millennial Model United Nations 22nd',
                'slug'              => 'global-millennial-model-united-nations-22nd',
                'category'          => 'Event & Publication',
                'year'              => '2026',
                'client'            => 'GMMUN Committee',
                'color'             => '#FD0000',
                'is_published'      => true,
                'is_featured'       => true,
                'featured'          => true,
                'sort_order'        => 1,
                'short_description' => 'Identitas visual diplomatik untuk konferensi pemuda internasional terbesar — sebuah perpaduan simbol keberanian dan semangat generasi muda.',
                'description'       => "Setiap delegasi membawa kisah tersendiri. Kami merancang sistem identitas visual yang menjadi bahasa universal di antara ratusan delegasi dari berbagai negara.\n\nDari bendera meja hingga sertifikat penghargaan, dari backdrop panggung hingga panduan konferensi — setiap elemen visual dirancang untuk mencerminkan semangat diplomasi, keberagaman, dan kepemimpinan generasi muda yang siap membentuk masa depan.",
                'tags'              => ['Event', 'Educational Event', 'Youth', 'Branding'],
                'tag_names'         => ['Event', 'Educational Event', 'Youth', 'Branding'],
                'media'             => [
                    [
                        'title'      => 'Official Conference Branding System',
                        'alt_text'   => 'Sistem identitas visual GMMUN 22nd — kombinasi merah diplomatik dan emas kepemimpinan',
                        'caption'    => 'Visual identity system — GMMUN 22nd Official Branding',
                        'sort_order' => 0,
                        'is_featured'=> true,
                    ],
                    [
                        'title'      => 'Certificate & Awards Design',
                        'alt_text'   => 'Desain sertifikat penghargaan untuk delegasi dan pemenang award GMMUN 22nd',
                        'caption'    => 'Award certificates — handcrafted for every delegate\'s achievement',
                        'sort_order' => 1,
                        'is_featured'=> false,
                    ],
                    [
                        'title'      => 'Stage Backdrop & Signage',
                        'alt_text'   => 'Backdrop panggung dan signage resmi konferensi GMMUN 22nd',
                        'caption'    => 'Stage production — where ideas take the spotlight',
                        'sort_order' => 2,
                        'is_featured'=> false,
                    ],
                ],
            ],
            [
                'num'               => '02',
                'title'             => 'Nusantara Soundscape Festival 2025',
                'slug'              => 'nusantara-soundscape-festival-2025',
                'category'          => 'Stage Visuals & Production',
                'year'              => '2025',
                'client'            => 'Nusantara Arts Collective',
                'color'             => '#C7983F',
                'is_published'      => true,
                'is_featured'       => true,
                'featured'          => true,
                'sort_order'        => 2,
                'short_description' => 'Tata panggung, animasi LED, dan kampanye visual untuk festival musik yang merayakan keragaman bunyi Nusantara.',
                'description'       => "Suara adalah budaya yang hidup. Festival ini memanggil seniman dari berbagai penjuru kepulauan untuk merayakan kekayaan soundscape Nusantara dalam satu panggung besar.\n\nKami hadir sebagai partner visual — merancang animasi motion grafik LED yang berpadu dengan ritme musik, material kampanye sosial media yang konsisten, serta identitas festival yang membawa jiwa Nusantara ke layar dan panggung secara bersamaan.",
                'tags'              => ['Event', 'Motion', 'Campaign', 'Branding'],
                'tag_names'         => ['Event', 'Motion', 'Animation', 'Campaign'],
                'media'             => [
                    [
                        'title'      => 'Festival Identity & Key Visual',
                        'alt_text'   => 'Key visual festival Nusantara Soundscape 2025 — motif batik bertemu tipografi modern',
                        'caption'    => 'Key visual — where tradition meets contemporary design',
                        'sort_order' => 0,
                        'is_featured'=> true,
                    ],
                    [
                        'title'      => 'LED Stage Motion Graphics',
                        'alt_text'   => 'Animasi motion grafik LED panggung utama festival Nusantara Soundscape 2025',
                        'caption'    => 'LED visuals — 270° immersive stage experience',
                        'sort_order' => 1,
                        'is_featured'=> false,
                    ],
                ],
            ],
            [
                'num'               => '03',
                'title'             => 'Karsa Identity System',
                'slug'              => 'karsa-identity-system',
                'category'          => 'Corporate & Brand Activation',
                'year'              => '2025',
                'client'            => 'Karsa Brand',
                'color'             => '#3B82F6',
                'is_published'      => true,
                'is_featured'       => false,
                'featured'          => false,
                'sort_order'        => 3,
                'short_description' => 'Perancangan identitas merek lengkap — dari konsep hingga panduan merek yang siap digunakan lintas medium.',
                'description'       => "Karsa bermakna keinginan kuat yang mendorong tindakan. Dengan nama yang kaya makna itu, kami membangun sistem identitas yang tidak hanya estetis, namun menyampaikan karakter merek secara autentik.\n\nProses dimulai dari riset mendalam tentang nilai merek, positioning, dan target audiens. Hasilnya adalah sistem visual yang kohesif — logo yang bersifat timeless, palet warna yang ekspresif, tipografi yang berkarakter, dan panduan merek lengkap yang siap diimplementasikan.",
                'tags'              => ['Branding', 'Corporate', 'Identity'],
                'tag_names'         => ['Branding', 'Corporate', 'Identity'],
                'media'             => [
                    [
                        'title'      => 'Logo System & Primary Lockup',
                        'alt_text'   => 'Sistem logo Karsa — primary lockup, monogram, dan variasi warna',
                        'caption'    => 'Logo system — built for versatility across all touchpoints',
                        'sort_order' => 0,
                        'is_featured'=> true,
                    ],
                    [
                        'title'      => 'Brand Guidelines Document',
                        'alt_text'   => 'Panduan merek Karsa — tipografi, palet warna, dan aturan penggunaan visual',
                        'caption'    => 'Brand guidelines — 48 pages of visual language documentation',
                        'sort_order' => 1,
                        'is_featured'=> false,
                    ],
                ],
            ],
            [
                'num'               => '04',
                'title'             => 'OSIS Leadership Summit — Visual Campaign',
                'slug'              => 'osis-leadership-summit-visual-campaign',
                'category'          => 'Event & Publication',
                'year'              => '2025',
                'client'            => 'OSIS SMA Negeri',
                'color'             => '#8B5CF6',
                'is_published'      => true,
                'is_featured'       => false,
                'featured'          => false,
                'sort_order'        => 4,
                'short_description' => 'Materi visual kampanye untuk summit kepemimpinan pelajar — menghidupkan semangat generasi muda yang siap memimpin.',
                'description'       => "Pemimpin masa depan dimulai dari ruang kelas. Summit ini mengumpulkan ratusan pengurus OSIS dari berbagai sekolah untuk saling belajar, berbagi visi, dan tumbuh bersama.\n\nKami merancang kampanye visual yang membangkitkan rasa percaya diri dan kebanggaan menjadi pemimpin muda — dari poster hero, media kit digital, hingga materi cetak yang dibawa pulang peserta sebagai kenangan.",
                'tags'              => ['Educational Event', 'Youth', 'Campaign', 'School'],
                'tag_names'         => ['Educational Event', 'Youth', 'Campaign', 'School'],
                'media'             => [
                    [
                        'title'      => 'Hero Poster Campaign',
                        'alt_text'   => 'Poster hero OSIS Leadership Summit — visual kepemimpinan yang berani dan inspiratif',
                        'caption'    => 'Hero poster — designed to inspire, not just inform',
                        'sort_order' => 0,
                        'is_featured'=> true,
                    ],
                ],
            ],
            [
                'num'               => '05',
                'title'             => 'University Research Publication Design',
                'slug'              => 'university-research-publication-design',
                'category'          => 'Event & Publication',
                'year'              => '2025',
                'client'            => 'Fakultas Ilmu Pendidikan',
                'color'             => '#10B981',
                'is_published'      => true,
                'is_featured'       => false,
                'featured'          => false,
                'sort_order'        => 5,
                'short_description' => 'Desain publikasi ilmiah dan proceedings konferensi yang mengubah riset akademik menjadi bacaan yang accessible dan elegan.',
                'description'       => "Penelitian terbaik seharusnya dapat dijangkau oleh semua orang. Kami memahami bahwa konten akademik yang padat membutuhkan tata letak yang cermat agar mudah dibaca, dipahami, dan diapresiasi.\n\nProyek ini mencakup perancangan layout proceedings konferensi internasional, desain cover jurnal ilmiah, serta infografis ringkasan penelitian yang memudahkan pembaca awam memahami temuan-temuan penting.",
                'tags'              => ['Academic', 'University', 'Print', 'Education'],
                'tag_names'         => ['Academic', 'University', 'Print', 'Education'],
                'media'             => [
                    [
                        'title'      => 'Proceedings Cover & Interior Layout',
                        'alt_text'   => 'Desain cover dan layout interior proceedings konferensi akademik internasional',
                        'caption'    => 'Academic proceedings — where knowledge meets design',
                        'sort_order' => 0,
                        'is_featured'=> true,
                    ],
                ],
            ],
        ];

        foreach ($works as $workData) {
            $tagNames  = $workData['tag_names'] ?? [];
            $mediaData = $workData['media'] ?? [];

            unset($workData['tag_names'], $workData['media']);

            // legacy json tags
            $workData['tags'] = $workData['tags'] ?? [];

            $work = Work::updateOrCreate(
                ['slug' => $workData['slug']],
                $workData
            );

            // Sync normalized tags
            if (!empty($tagNames)) {
                $tagIds = [];
                foreach ($tagNames as $name) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($name)],
                        ['name' => $name]
                    );
                    $tagIds[] = $tag->id;
                }
                $work->normalizedTags()->syncWithoutDetaching($tagIds);
            }

            // Insert media records (skip if already exist)
            if ($work->wasRecentlyCreated || $work->media()->count() === 0) {
                $work->media()->delete();
                foreach ($mediaData as $m) {
                    WorkMedia::create([
                        'work_id'    => $work->id,
                        'type'       => 'image',
                        'path'       => 'works/placeholder.jpg', // placeholder — ganti via admin
                        'title'      => $m['title'],
                        'alt_text'   => $m['alt_text'],
                        'caption'    => $m['caption'],
                        'sort_order' => $m['sort_order'],
                        'is_featured'=> $m['is_featured'],
                    ]);
                }
            }
        }
    }
}
