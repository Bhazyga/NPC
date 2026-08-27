<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkRequest;
use App\Http\Requests\UpdateWorkRequest;
use App\Models\Work;
use App\Models\WorkMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkController extends Controller
{
    /**
     * GET /api/works — Public listing (published, supports search & category filter).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Work::where('is_published', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->where('category', $request->input('category'));
        }

        if ($request->boolean('featured_only') || $request->boolean('is_featured')) {
            $query->where('is_featured', true);
        }

        $works = $query
            ->with(['normalizedTags', 'media'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        $works = $works->map(function ($work) {
            $arr = $work->toArray();
            $arr['youtube_embed_url'] = $work->youtube_embed_url;
            return $arr;
        });

        return response()->json($works);
    }

    /**
     * GET /api/works/{slug} — Public work detail with eager loading.
     */
    public function show(Work $work): JsonResponse
    {
        if (!$work->is_published) {
            abort(404);
        }

        $work->load(['media', 'normalizedTags', 'links', 'services' => function ($q) {
            $q->where('is_published', true);
        }]);

        $arr = $work->toArray();
        $arr['youtube_embed_url'] = $work->youtube_embed_url;

        return response()->json($arr);
    }

    /**
     * POST /api/admin/works — Admin create.
     */
    public function store(StoreWorkRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = !empty($data['slug'])
            ? $data['slug']
            : $this->generateUniqueSlug($data['title']);

        // Handle cover/hero image file upload
        if ($request->hasFile('cover_image_file')) {
            $data['cover_image'] = $request->file('cover_image_file')->store('works/covers', 'public');
        }
        if ($request->hasFile('hero_image_file')) {
            $data['hero_image'] = $request->file('hero_image_file')->store('works/heroes', 'public');
        }

        $work = Work::create($data);

        $this->syncRelations($work, $data);
        $this->syncMedia($work, $request);

        $work->load(['media', 'normalizedTags', 'links', 'services']);

        $arr = $work->toArray();
        $arr['youtube_embed_url'] = $work->youtube_embed_url;

        return response()->json(['message' => 'Karya berhasil ditambahkan', 'data' => $arr], 201);
    }

    /**
     * PUT /api/admin/works/{work} — Admin update (supports multipart/form-data).
     */
    public function update(UpdateWorkRequest $request, Work $work): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('slug', $data) && empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $work->id);
        }

        // Handle cover/hero image file upload — delete old file if replaced
        if ($request->hasFile('cover_image_file')) {
            if ($work->cover_image) Storage::disk('public')->delete($work->cover_image);
            $data['cover_image'] = $request->file('cover_image_file')->store('works/covers', 'public');
        }
        if ($request->hasFile('hero_image_file')) {
            if ($work->hero_image) Storage::disk('public')->delete($work->hero_image);
            $data['hero_image'] = $request->file('hero_image_file')->store('works/heroes', 'public');
        }

        $work->update($data);
        $this->syncRelations($work, $data);
        $this->syncMedia($work, $request);

        $work->load(['media', 'normalizedTags', 'links', 'services']);

        $arr = $work->toArray();
        $arr['youtube_embed_url'] = $work->youtube_embed_url;

        return response()->json(['message' => 'Karya berhasil diperbarui', 'data' => $arr]);
    }

    /**
     * DELETE /api/admin/works/{work} — Admin delete.
     */
    public function destroy(Work $work): JsonResponse
    {
        $work->links()->delete();
        $work->delete();

        return response()->json(['message' => 'Karya berhasil dihapus']);
    }

    // ── Admin index: all works including unpublished ─────
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Work::with(['normalizedTags', 'media']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->where('category', $request->input('category'));
        }

        if ($request->boolean('featured_only')) {
            $query->where('is_featured', true);
        }

        $works = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->get();

        $works = $works->map(function ($work) {
            $arr = $work->toArray();
            $arr['youtube_embed_url'] = $work->youtube_embed_url;
            return $arr;
        });

        return response()->json($works);
    }

    // ── Admin: delete a single gallery image ─────────────
    public function destroyMedia(Work $work, WorkMedia $media): JsonResponse
    {
        if ($media->work_id !== $work->id) abort(403);
        Storage::disk('public')->delete($media->path);
        $media->delete();
        return response()->json(['message' => 'Gambar berhasil dihapus']);
    }

    // ── Private helpers ──────────────────────────────────

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (Work::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function syncRelations(Work $work, array $data): void
    {
        if (array_key_exists('tag_ids', $data)) {
            $work->normalizedTags()->sync($data['tag_ids'] ?? []);
        }

        if (array_key_exists('service_ids', $data)) {
            $pivotData = collect($data['service_ids'] ?? [])->mapWithKeys(fn ($id, $i) => [$id => ['sort_order' => $i]]);
            $work->services()->sync($pivotData);
        }

        if (array_key_exists('links', $data)) {
            $work->links()->delete();
            foreach ($data['links'] ?? [] as $i => $link) {
                $work->links()->create([
                    'label'      => $link['label'],
                    'url'        => $link['url'],
                    'type'       => $link['type'] ?? null,
                    'sort_order' => $link['sort_order'] ?? $i,
                ]);
            }
        }
    }

    /**
     * Handle gallery image uploads from multipart form.
     * Expects: gallery_images[] = File[]
     */
    private function syncMedia(Work $work, Request $request): void
    {
        if (!$request->hasFile('gallery_images')) return;

        $existing = $work->media()->count();

        foreach ($request->file('gallery_images') as $i => $file) {
            $path = $file->store('works/gallery', 'public');
            WorkMedia::create([
                'work_id'    => $work->id,
                'type'       => 'image',
                'path'       => $path,
                'title'      => $request->input("gallery_titles.{$i}"),
                'alt_text'   => $request->input("gallery_alts.{$i}"),
                'caption'    => $request->input("gallery_captions.{$i}"),
                'sort_order' => $existing + $i,
            ]);
        }
    }
}
