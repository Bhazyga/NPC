<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkRequest;
use App\Http\Requests\UpdateWorkRequest;
use App\Models\Work;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

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

        $work = Work::create($data);

        $this->syncRelations($work, $data);

        $work->load(['media', 'normalizedTags', 'links', 'services']);

        return response()->json(['message' => 'Karya berhasil ditambahkan', 'data' => $work], 201);
    }

    /**
     * PUT /api/admin/works/{work} — Admin update.
     */
    public function update(UpdateWorkRequest $request, Work $work): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('slug', $data) && empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $work->id);
        }

        $work->update($data);
        $this->syncRelations($work, $data);

        $work->load(['media', 'normalizedTags', 'links', 'services']);

        return response()->json(['message' => 'Karya berhasil diperbarui', 'data' => $work]);
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
        $query = Work::query();

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

        return response()->json($works);
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
}
