<?php

namespace App\Http\Controllers;

use App\Models\PreTripChecklistItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PreTripChecklistItemController extends Controller
{
    public function index(): View
    {
        return view('pre-trip-checklist-items.index', [
            'items' => PreTripChecklistItem::query()->ordered()->get(),
            'item' => new PreTripChecklistItem([
                'sort_order' => (PreTripChecklistItem::max('sort_order') ?? 0) + 10,
                'is_active' => true,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['key'] = $this->uniqueKey($validated['label']);
        $validated['is_active'] = $request->boolean('is_active');

        PreTripChecklistItem::create($validated);

        return redirect()->route('pre-trip-checklist-items.index')->with('success', 'เพิ่มรายการตรวจเช็กเรียบร้อยแล้ว');
    }

    public function update(Request $request, PreTripChecklistItem $preTripChecklistItem): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active');

        $preTripChecklistItem->update($validated);

        return redirect()->route('pre-trip-checklist-items.index')->with('success', 'อัปเดตรายการตรวจเช็กเรียบร้อยแล้ว');
    }

    public function destroy(PreTripChecklistItem $preTripChecklistItem): RedirectResponse
    {
        $preTripChecklistItem->delete();

        return redirect()->route('pre-trip-checklist-items.index')->with('success', 'ลบรายการตรวจเช็กเรียบร้อยแล้ว');
    }

    private function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:1000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', Rule::in(['1', '0', 'on'])],
        ];
    }

    private function uniqueKey(string $label): string
    {
        $base = Str::slug(Str::limit($label, 40, ''), '_') ?: 'check_item';
        $key = $base;
        $running = 2;

        while (PreTripChecklistItem::where('key', $key)->exists()) {
            $key = $base . '_' . $running;
            $running++;
        }

        return $key;
    }
}
