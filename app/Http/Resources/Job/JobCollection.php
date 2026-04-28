<?php
namespace App\Http\Resources\Job;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JobCollection extends ResourceCollection
{
    public $collects = JobResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total'        => $this->resource->total(),
                'per_page'     => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page'    => $this->resource->lastPage(),
                'links'        => $this->resource->linkCollection()->toArray(),
            ],
        ];
    }
}
