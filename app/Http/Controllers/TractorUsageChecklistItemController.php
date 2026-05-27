<?php

namespace App\Http\Controllers;

use App\Models\TractorUsageChecklistItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TractorUsageChecklistItemController extends Controller
{
    public function index(): View
    {
        return view('tractor-usage-checklist-items.index', [
            'items' => TractorUsageChecklistItem::query()->ordered()->get(),
            'item' => new TractorUsageChecklistItem([
                'group_key' => 'engine_system',
                'group_label' => 'ระบบเครื่องยนต์',
                'sort_order' => (TractorUsageChecklistItem::max('sort_order') ?? 0) + 10,
                'is_active' => true,
            ]),
            'groups' => TractorUsageChecklistItem::query()
                ->select('group_key', 'group_label')
                ->distinct()
                ->orderBy('group_label')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['key'] = $this->uniqueKey($validated['label']);
        $validated['is_active'] = $request->boolean('is_active');

        TractorUsageChecklistItem::create($validated);

        return redirect()
            ->route('tractor-usage-checklist-items.index')
            ->with('success', 'เพิ่มรายการตรวจเช็กรถไถเรียบร้อยแล้ว');
    }

    public function update(Request $request, TractorUsageChecklistItem $tractorUsageChecklistItem): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active');

        $tractorUsageChecklistItem->update($validated);

        return redirect()
            ->route('tractor-usage-checklist-items.index')
            ->with('success', 'อัปเดตรายการตรวจเช็กรถไถเรียบร้อยแล้ว');
    }

    public function destroy(TractorUsageChecklistItem $tractorUsageChecklistItem): RedirectResponse
    {
        $tractorUsageChecklistItem->delete();

        return redirect()
            ->route('tractor-usage-checklist-items.index')
            ->with('success', 'ลบรายการตรวจเช็กรถไถเรียบร้อยแล้ว');
    }

    private function rules(): array
    {
        return [
            'group_key' => ['required', 'string', 'max:120'],
            'group_label' => ['required', 'string', 'max:255'],
            'label' => ['required', 'string', 'max:1000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', Rule::in(['1', '0', 'on'])],
        ];
    }

    private function uniqueKey(string $label): string
    {
        $base = Str::slug(Str::limit($label, 40, ''), '_') ?: 'tractor_check_item';
        $key = $base;
        $running = 2;

        while (TractorUsageChecklistItem::where('key', $key)->exists()) {
            $key = $base . '_' . $running;
            $running++;
        }

        return $key;
    }
}
