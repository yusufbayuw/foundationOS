<?php

namespace Modules\School\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return $this->moduleView('school::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return $this->moduleView('school::create');
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
        return $this->moduleView('school::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int|string $id): View
    {
        return $this->moduleView('school::edit');
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
