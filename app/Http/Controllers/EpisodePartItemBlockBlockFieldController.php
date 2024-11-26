<?php

namespace App\Http\Controllers;

use App\Http\Requests\EpisodePartItemBlockBlockFieldRequest;
use App\Http\Resources\BlockFieldResource;
use App\Models\Block;
use App\Models\BlockField;
use App\Models\Episode;
use App\Models\Item;
use App\Models\Part;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class EpisodePartItemBlockBlockFieldController extends Controller
{
    public function index(Episode $episode, Part $part, Item $item, Block $block): AnonymousResourceCollection
    {
        logger(__METHOD__ . ' triggered');

        return BlockFieldResource::collection($block->blockFields()->paginate(request('per_page', 10)));
    }

    public function store(EpisodePartItemBlockBlockFieldRequest $request, Episode $episode, Part $part, Item $item, Block $block): BlockFieldResource
    {
        logger(__METHOD__ . ' triggered');

        return new BlockFieldResource($block->blockFields()->create($request->all()));
    }

    public function show(Episode $episode, Part $part, Item $item, Block $block, BlockField $blockField): BlockFieldResource
    {
        logger(__METHOD__ . ' triggered');

        return new BlockFieldResource($blockField);
    }

    public function update(EpisodePartItemBlockBlockFieldRequest $request, Episode $episode, Part $part, Item $item, Block $block, BlockField $blockField): BlockFieldResource
    {
        logger(__METHOD__ . ' triggered');

        $blockField->update($request->all());

        return new BlockFieldResource($blockField);
    }

    public function destroy(Episode $episode, Part $part, Item $item, Block $block, BlockField $blockField): Response
    {
        logger(__METHOD__ . ' triggered');

        $blockField->delete();

        return response()->noContent();
    }
}
