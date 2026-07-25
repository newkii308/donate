<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(private readonly ActivityLogService $activity)
    {
    }

    /** รายการหน้าเนื้อหาทั้งหมด */
    public function index(): View
    {
        $counts = ContentBlock::selectRaw('page, count(*) as total')
            ->groupBy('page')->pluck('total', 'page');

        return view('admin.content.index', [
            'pages' => ContentBlock::PAGES,
            'counts' => $counts,
        ]);
    }

    /** แก้ไขบล็อกทั้งหมดของหน้าหนึ่ง */
    public function edit(string $page): View
    {
        abort_unless(array_key_exists($page, ContentBlock::PAGES), 404);

        return view('admin.content.edit', [
            'page' => $page,
            'pageLabel' => ContentBlock::PAGES[$page],
            'blocks' => ContentBlock::forPage($page)->orderBy('sort_order')->get(),
            'types' => ContentBlock::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $data['sort_order'] = (int) ContentBlock::forPage($data['page'])->max('sort_order') + 1;

        ContentBlock::create($data);

        $this->activity->log('content.block.created', "Added {$data['type']} block to {$data['page']}");

        return back()->with('success', 'ເພີ່ມບລັອກສຳເລັດແລ້ວ');
    }

    public function update(Request $request, ContentBlock $block): RedirectResponse
    {
        $block->update($this->validated($request, $block));

        $this->activity->log('content.block.updated', "Updated block #{$block->id} on {$block->page}");

        return back()->with('success', 'ບັນທຶກບລັອກສຳເລັດແລ້ວ');
    }

    public function destroy(ContentBlock $block): RedirectResponse
    {
        $page = $block->page;
        $block->delete();

        $this->activity->log('content.block.deleted', "Deleted block from {$page}");

        return back()->with('success', 'ລຶບບລັອກສຳເລັດແລ້ວ');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?ContentBlock $block = null): array
    {
        $request->merge(['is_visible' => $request->boolean('is_visible')]);

        return $request->validate([
            'page' => [$block ? 'sometimes' : 'required', Rule::in(array_keys(ContentBlock::PAGES))],
            'type' => ['required', Rule::in(array_keys(ContentBlock::TYPES))],
            'heading' => ['nullable', 'string', 'max:255'],
            'subheading' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:20000'],
            'image_url' => ['nullable', 'string', 'max:1000'],
            'link_label' => ['nullable', 'string', 'max:120'],
            'link_url' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_visible' => ['boolean'],
        ]);
    }
}
