<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddAgeGroupRequest;
use App\Models\AgeGroup;
use Illuminate\Http\Request;
use Throwable;

class AgeGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddAgeGroupRequest $request)
    {
        $field = $request->validated();
        try {
            AgeGroup::create($field);

        } catch (Throwable $th) {
            return $this->error($th, 'Failed to create new age group.');
        }
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AgeGroup $ageGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AgeGroup $ageGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AgeGroup $ageGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AgeGroup $ageGroup)
    {
        //
    }
}
