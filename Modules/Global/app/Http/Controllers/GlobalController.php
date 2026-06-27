<?php

namespace Modules\Global\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->moduleView('global::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->moduleView('global::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return $this->moduleView('global::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return $this->moduleView('global::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
