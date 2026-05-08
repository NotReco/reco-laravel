<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index(Request $request)
    {
        $query = Person::query()->withCount(['movies as movies_count', 'movies as actor_count' => function ($q) {
            $q->where('movie_person.role', 'actor');
        }]);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('role')) {
            $query->whereHas('movies', fn($q) => $q->where('movie_person.role', $request->role));
        }

        $people = $query->orderByDesc('created_at')->paginate(24)->withQueryString();

        return view('admin.people.index', compact('people'));
    }

    public function edit(Person $person)
    {
        $person->load(['movies' => fn($q) => $q->withPivot('role', 'character_name', 'display_order')->orderByPivot('display_order')]);

        return view('admin.people.edit', compact('person'));
    }

    public function update(Request $request, Person $person)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'photo'          => 'nullable|url|max:2048',
            'biography'      => 'nullable|string',
            'date_of_birth'  => 'nullable|date',
            'date_of_death'  => 'nullable|date|after_or_equal:date_of_birth',
            'nationality'    => 'nullable|string|max:100',
            'place_of_birth' => 'nullable|string|max:255',
            'known_for'      => 'nullable|string|max:100',
            'gender'         => 'nullable|integer|in:0,1,2,3',
            'homepage'       => 'nullable|url|max:2048',
            'imdb_id'        => 'nullable|string|max:20',
            'instagram_id'   => 'nullable|string|max:100',
            'twitter_id'     => 'nullable|string|max:100',
        ]);

        $person->update($validated);

        return redirect()
            ->route('admin.people.index')
            ->with('success', "Đã cập nhật thông tin «{$person->name}».");
    }

    public function destroy(Person $person)
    {
        $name = $person->name;
        $person->deleteOrFail();

        return redirect()
            ->route('admin.people.index')
            ->with('success', "Đã xóa «{$name}» khỏi hệ thống.");
    }
}
