<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartRequest;
use App\Http\Resources\PartResource;
use App\Models\Part;
use DB;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class PartController extends Controller
{
    public function index(): ResourceCollection
    {
        logger(__METHOD__ . ' triggered');

        return PartResource::collection(Part::paginate(request('per_page', 10)));
    }

    public function store(PartRequest $request): PartResource
    {
        logger(__METHOD__ . ' triggered');

        return new PartResource(Part::create($request->all()));
    }

    public function show(Part $part): PartResource
    {
        logger(__METHOD__ . ' triggered');

        return new PartResource($part);
    }

    /**
     * @throws Throwable
     */
    public function update(PartRequest $request, Part $part): PartResource
    {
        logger(__METHOD__ . ' triggered');

        // We need to be inside a transaction because we are updating multiple rows,
        // and we have a unique constraint on episode_id and position
        DB::transaction(function () use ($request, $part) {
            $part->update($request->all());
        });

        return new PartResource($part);
    }

    /**
     * @throws Throwable
     */
    public function destroy(Part $part): Response
    {
        logger(__METHOD__ . ' triggered');

        // We need to be inside a transaction because we are updating multiple rows,
        // and we have a unique constraint on episode_id and position
        DB::transaction(function () use ($part) {
            $part->delete();
        });

        return response()->noContent();
    }
}
