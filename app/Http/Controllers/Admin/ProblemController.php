<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Models\ProblemSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProblemController extends Controller
{
    public function index()
    {
        $problems = Problem::withCount('slots')->get();
        return view('admin.problems.index', compact('problems'));
    }

    public function create()
    {
        return view('admin.problems.create');
    }

    public function store(Request $request)
    {
        try {
            $problem = Problem::create([
                'question_text' => $request->input('question_text', '問題文を入力してください'),
                'countdown_seconds' => $request->input('countdown_seconds', 60),
            ]);

            for ($i = 1; $i <= 10; $i++) {
                $imagePath = null;
                $imageFiles = $request->file('images') ?? [];
                if (!empty($imageFiles[$i]) && $imageFiles[$i]->isValid()) {
                    $imagePath = $imageFiles[$i]->store("problem-images/{$problem->id}", 'public');
                }

                ProblemSlot::create([
                    'problem_id' => $problem->id,
                    'slot_number' => $i,
                    'image_path' => $imagePath ?: null,
                    'answer_text' => $request->input("slots.{$i}.answer_text", ''),
                    'is_correct' => $request->boolean("slots.{$i}.is_correct"),
                ]);
            }

            return redirect()->route('admin.problems.edit', $problem)->with('success', '問題を作成しました');
        } catch (\Exception $e) {
            \Log::error('Problem store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withInput()
                ->with('error', '保存中にエラーが発生しました: ' . $e->getMessage());
        }
    }

    public function edit(Problem $problem)
    {
        $problem->load('slots');
        return view('admin.problems.edit', compact('problem'));
    }

    public function update(Request $request, Problem $problem)
    {
        $problem->update([
            'question_text' => $request->input('question_text'),
            'countdown_seconds' => $request->input('countdown_seconds', 60),
        ]);

        foreach ($problem->slots as $slot) {
            $i = $slot->slot_number;
            $slot->update([
                'answer_text' => $request->input("slots.{$i}.answer_text", ''),
                'is_correct' => $request->boolean("slots.{$i}.is_correct"),
            ]);
        }

        return redirect()->route('admin.problems.edit', $problem)->with('success', '保存しました');
    }

    public function destroy(Problem $problem)
    {
        foreach ($problem->slots as $slot) {
            if ($slot->image_path) {
                Storage::disk('public')->delete($slot->image_path);
            }
        }
        $problem->delete();

        return redirect()->route('admin.problems.index')->with('success', '削除しました');
    }

    public function uploadImage(Request $request, Problem $problem, ProblemSlot $slot)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'ファイルがありません'], 422);
        }

        if ($slot->image_path) {
            Storage::disk('public')->delete($slot->image_path);
        }

        $path = $request->file('image')->store("problem-images/{$problem->id}", 'public');
        $slot->update(['image_path' => $path]);

        return response()->json(['url' => asset('storage/' . $path)]);
    }

    public function deleteImage(Problem $problem, ProblemSlot $slot)
    {
        if ($slot->image_path) {
            Storage::disk('public')->delete($slot->image_path);
            $slot->update(['image_path' => null]);
        }

        return response()->json(['ok' => true]);
    }
}
