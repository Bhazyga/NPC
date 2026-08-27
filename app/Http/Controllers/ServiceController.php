<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Models\ServiceMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * GET /api/services — Public listing (published only, ordered by sort_order).
     */
    public function index(): JsonResponse
    {
        $services = Service::where('is_published', true)
            ->with(['items', 'tags', 'media'])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return response()->json($services);
    }

    /**
     * GET /api/services/{slug} — Public service detail, eager-loaded, no N+1.
     */
    public function show(Service $service): JsonResponse
    {
        if (!$service->is_published) {
            abort(404);
        }

        $service->load(['items', 'media', 'tags', 'links', 'works' => function ($q) {
            $q->where('is_published', true)->with(['normalizedTags']);
        }]);

        return response()->json($this->formatService($service));
    }

    /**
     * POST /api/admin/services — Admin create.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = !empty($data['slug']) 
            ? $data['slug'] 
            : $this->generateUniqueSlug($data['title']);

        // Handle hero image file upload
        if ($request->hasFile('hero_image_file')) {
            $path = $request->file('hero_image_file')->store('services/heroes', 'public');
            $data['hero_image'] = Storage::disk('public')->url($path);
        }

        $service = Service::create($data);

        $this->syncRelations($service, $data);
        $this->syncMedia($service, $request);

        $service->load(['items', 'media', 'tags', 'links', 'works']);

        return response()->json(['message' => 'Service berhasil dibuat', 'data' => $service], 201);
    }

    /**
     * PUT /api/admin/services/{service} — Admin update.
     */
    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $data = $request->validated();

        if (array_key_exists('slug', $data) && empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $service->id);
        }

        // Handle hero image file upload
        if ($request->hasFile('hero_image_file')) {
            if ($service->hero_image && Str::contains($service->hero_image, '/storage/')) {
                $oldPath = Str::after($service->hero_image, '/storage/');
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('hero_image_file')->store('services/heroes', 'public');
            $data['hero_image'] = Storage::disk('public')->url($path);
        }

        $service->update($data);
        $this->syncRelations($service, $data);
        $this->syncMedia($service, $request);

        $service->load(['items', 'media', 'tags', 'links', 'works']);

        return response()->json(['message' => 'Service berhasil diperbarui', 'data' => $service]);
    }

    /**
     * DELETE /api/admin/services/{service} — Admin delete.
     */
    public function destroy(Service $service): JsonResponse
    {
        foreach ($service->media as $m) {
            Storage::disk('public')->delete($m->path);
        }
        $service->media()->delete();
        $service->links()->delete();
        $service->delete();

        return response()->json(['message' => 'Service berhasil dihapus']);
    }

    /**
     * DELETE /api/admin/services/{service}/media/{media} — Delete specific gallery image.
     */
    public function destroyMedia(Service $service, ServiceMedia $media): JsonResponse
    {
        if ($media->service_id !== $service->id) {
            return response()->json(['message' => 'Media tidak terhubung dengan service ini.'], 422);
        }

        Storage::disk('public')->delete($media->path);
        $media->delete();

        return response()->json(['message' => 'Gambar gallery berhasil dihapus']);
    }

    // ── Admin: all services (including unpublished) ──────
    public function adminIndex(): JsonResponse
    {
        $services = Service::with(['items', 'tags', 'media', 'links'])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return response()->json($services);
    }

    // ── Private helpers ──────────────────────────────────

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (Service::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function syncRelations(Service $service, array $data): void
    {
        if (array_key_exists('tag_ids', $data)) {
            $service->tags()->sync($data['tag_ids'] ?? []);
        }

        if (array_key_exists('work_ids', $data)) {
            $pivotData = collect($data['work_ids'] ?? [])->mapWithKeys(fn ($id, $i) => [$id => ['sort_order' => $i]]);
            $service->works()->sync($pivotData);
        }

        if (array_key_exists('items', $data)) {
            $service->items()->delete();
            foreach ($data['items'] ?? [] as $i => $item) {
                $service->items()->create([
                    'title'       => $item['title'],
                    'description' => $item['description'] ?? null,
                    'sort_order'  => $item['sort_order'] ?? $i,
                ]);
            }
        }

        if (array_key_exists('links', $data)) {
            $service->links()->delete();
            foreach ($data['links'] ?? [] as $i => $link) {
                $service->links()->create([
                    'label'      => $link['label'],
                    'url'        => $link['url'],
                    'type'       => $link['type'] ?? null,
                    'sort_order' => $link['sort_order'] ?? $i,
                ]);
            }
        }
    }

    private function syncMedia(Service $service, Request $request): void
    {
        if (!$request->hasFile('gallery_images')) {
            return;
        }

        $files    = $request->file('gallery_images');
        $titles   = $request->input('gallery_titles', []);
        $alts     = $request->input('gallery_alts', []);
        $captions = $request->input('gallery_captions', []);

        $maxSort = (int) $service->media()->max('sort_order');

        foreach ($files as $index => $file) {
            $path = $file->store('services/gallery', 'public');
            $service->media()->create([
                'type'       => 'image',
                'path'       => $path,
                'title'      => $titles[$index]   ?? null,
                'alt_text'   => $alts[$index]     ?? null,
                'caption'    => $captions[$index] ?? null,
                'sort_order' => $maxSort + 1 + $index,
            ]);
        }
    }

    private function formatService(Service $service): array
    {
        $arr = $service->toArray();
        $arr['youtube_embed_url'] = $service->youtube_embed_url;
        return $arr;
    }
}
