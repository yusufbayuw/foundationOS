<?php

namespace Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return $this->moduleView('library::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return $this->moduleView('library::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void {}

    /**
     * Show the specified resource.
     */
    public function show(int|string $id): View
    {
        return $this->moduleView('library::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int|string $id): View
    {
        return $this->moduleView('library::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int|string $id): void {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int|string $id): void {}
}
