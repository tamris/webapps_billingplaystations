<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Playstation;
use Illuminate\Http\Request;
use App\Http\Requests\StorePlaystationRequest;
use App\Http\Requests\UpdatePlaystationRequest;

class PlaystationController extends Controller
{
    // FRONTEND LIST (admin & operator)
    public function index(Request $request)
    {
        $q = $request->get('q');
        $ps = Playstation::when($q, fn($query)=>$query
                ->where('code','like',"%$q%")
                ->orWhere('name','like',"%$q%"))
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pages.playstations.index', compact('ps','q'));
    }

    // FORM CREATE (admin only)
    public function create() { return view('admin.pages.playstations.create'); }

    public function store(StorePlaystationRequest $request)
    {
        Playstation::create($request->validated());
        return redirect()->route('playstations.index')->with('success', 'Data berhasil dihapus!');
    }

    public function edit(Playstation $playstation)
    {
        return view('admin.pages.playstations.edit', compact('playstation'));
    }

    public function update(UpdatePlaystationRequest $request, Playstation $playstation)
    {
        $playstation->update($request->validated());
        return redirect()->route('playstations.index')->with('success','PS berhasil diperbarui');
    }

    public function destroy(Playstation $playstation)
    {
        $playstation->delete();
        return back()->with('ok','PS dihapus');
    }
}