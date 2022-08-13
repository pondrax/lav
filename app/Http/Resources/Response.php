<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class Response extends ResourceCollection
{
    private $meta;

    private $status_code;

    private $message;

    public function __construct($resource, $status_code = 200, $message = 'Success')
    {
        $this->status_code = $status_code;
        $this->message = $message;

        if ($resource instanceof LengthAwarePaginator) {
            $this->meta = [
                'current_page' => $resource->currentPage(),
                'last_page' => $resource->lastPage(),
                'per_page' => $resource->perPage(),
                'count' => $resource->count(),
                'total' => $resource->total(),
                // 'to' => $resource->to(),
            ];
            $resource = $resource->getCollection();
        } elseif ($resource instanceof Builder) {
            // var_dump('builder');
            $resource = $resource->firstOrFail()->toArray();
        } elseif ($resource instanceof Model) {
            $resource = $resource->toArray();
        }

        if (! $this->meta) {
            $this->meta = [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => count($resource),
                'count' => count($resource),
                'total' => count($resource),
                // 'to' => $resource->to(),
            ];
        }
        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'message' => $this->message,
            'status_code' => $this->status_code,
            'data' => $this->collection,
            'meta' => $this->meta,
        ];
    }
}
