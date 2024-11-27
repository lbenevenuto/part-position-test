<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartRequest;
use App\Http\Requests\PartSortRequest;
use App\Http\Resources\PartResource;
use App\Models\Episode;
use App\Models\Part;
use DB;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class EpisodePartController extends Controller
{
    public function index(Episode $episode): ResourceCollection
    {
        logger(__METHOD__ . ' triggered');

        return PartResource::collection($episode->parts()->paginate(request('per_page', 10)));
    }

    public function store(PartRequest $request, Episode $episode): PartResource
    {
        logger(__METHOD__ . ' triggered');

        return new PartResource($episode->parts()->create($request->all()));
    }

    public function show(Episode $episode, Part $part): PartResource
    {
        logger(__METHOD__ . ' triggered');

        return new PartResource($part);
    }

    /**
     * @throws Throwable
     */
    public function update(PartRequest $request, Episode $episode, Part $part): PartResource
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
    public function destroy(Episode $episode, Part $part): Response
    {
        logger(__METHOD__ . ' triggered');

        // We need to be inside a transaction because we are updating multiple rows,
        // and we have a unique constraint on episode_id and position
        DB::transaction(function () use ($part) {
            $part->delete();
        });

        return response()->noContent();
    }

    public function sort(PartSortRequest $request, Episode $episode): Response
    {
        logger(__METHOD__ . ' triggered');

        $episode->sortParts($request->positions);

        return response()->noContent();
    }
}
