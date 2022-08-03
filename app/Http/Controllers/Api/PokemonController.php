<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PokemonResource;
use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PokemonController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $pokemon = Pokemon::paginate(10);

        return $this->sendResponse(PokemonResource::collection($pokemon), 'Pokemon retrieved successfully.', true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $pokemon = Pokemon::findOrFail($id);

        return $this->sendResponse(new PokemonResource($pokemon), 'Pokemon details retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $pokemon = Pokemon::find($id);

        if ($pokemon) {
            try {
                $pokemon->update([
                    'number' => $request->number,
                    'name' => $request->name,
                    'sprite_url' => $request->sprite_url,
                ]);

                return $this->sendResponse([], 'Pokemon updated.');
            } catch (\Exception $exception) {
                Log::info($exception->getMessage());
                return $this->sendError('Error attempting to update pokemon.', [], 500);
            }
        }

        return $this->sendError('Pokemon not found.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $pokemon = Pokemon::find($id);

        if ($pokemon && $pokemon->delete()) {
            return $this->sendResponse([], 'Pokemon deleted.');
        }

        return $this->sendError('Error attempting to delete pokemon.', [], 500);
    }
}
