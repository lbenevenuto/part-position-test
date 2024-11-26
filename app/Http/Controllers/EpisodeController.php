<?php

namespace App\Http\Controllers;

use App\Http\Requests\EpisodeRequest;
use App\Http\Resources\EpisodeResource;
use App\Models\Episode;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class EpisodeController extends Controller
{
    public function index(): ResourceCollection
    {
        logger(__METHOD__ . ' triggered');

        return EpisodeResource::collection(Episode::paginate(request('per_page', 10)));
    }

    public function store(EpisodeRequest $request): EpisodeResource
    {
        logger(__METHOD__ . ' triggered');

        return new EpisodeResource(Episode::create($request->all()));
    }

    public function show(Episode $episode): EpisodeResource
    {
        logger(__METHOD__ . ' triggered');

        return new EpisodeResource($episode);

    }

    public function update(EpisodeRequest $request, Episode $episode): EpisodeResource
    {
        logger(__METHOD__ . ' triggered');

        $episode->update($request->all());

        return new EpisodeResource($episode);
    }

    public function destroy(Episode $episode): Response
    {
        logger(__METHOD__ . ' triggered');

        $episode->delete();

        return response()->noContent();
    }

    public function duplicate(Episode $episode): EpisodeResource
    {
        logger(__METHOD__ . ' triggered');

        $newEpisode = $episode->duplicate();

        return new EpisodeResource($newEpisode);
    }
}
