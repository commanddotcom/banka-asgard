<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DonorNameRequest;
use App\Models\DonorName;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DonorNameController extends Controller
{
    public function index(): View
    {
        $names = DonorName::query()->orderBy('name')->get();

        return view('admin.donor-names.index', ['names' => $names]);
    }

    public function create(): View
    {
        return view('admin.donor-names.create');
    }

    public function store(DonorNameRequest $request): RedirectResponse
    {
        DonorName::create($request->validated());

        return redirect()->route('admin.names.index')->with('status', "Ім'я додано.");
    }

    public function edit(DonorName $donorName): View
    {
        return view('admin.donor-names.edit', ['donorName' => $donorName]);
    }

    public function update(DonorNameRequest $request, DonorName $donorName): RedirectResponse
    {
        $donorName->update($request->validated());

        return redirect()->route('admin.names.index')->with('status', "Ім'я оновлено.");
    }

    public function destroy(DonorName $donorName): RedirectResponse
    {
        $donorName->delete();

        return redirect()->route('admin.names.index')->with('status', "Ім'я видалено.");
    }
}
