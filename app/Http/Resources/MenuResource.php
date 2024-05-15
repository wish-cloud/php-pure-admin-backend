<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MenuResource extends ResourceCollection
{
    public $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'parentId' => $item->parent_id,
                'menuType' => $item->type,
                'title' => $item->title,
                'name' => $item->name,
                'path' => $item->path,
                'rank' => $item->sort,
                'isShow' => $item->is_show,
                'redirect' => $item->meta['redirect'] ?? '',
                'component' => $item->meta['component'] ?? '',
                'icon' => $item->meta['icon'] ?? '',
                'extraIcon' => $item->meta['extraIcon'] ?? '',
                'enterTransition' => $item->meta['enterTransition'] ?? '',
                'activePath' => $item->meta['activePath'] ?? '',
                'frameSrc' => $item->meta['frameSrc'] ?? '',
                'frameLoading' => $item->meta['frameLoading'] ?? true,
                'keepAlive' => $item->meta['keepAlive'] ?? false,
                'hiddenTag' => $item->meta['hiddenTag'] ?? false,
                'fixedTag' => $item->meta['fixedTag'] ?? false,
                'showParent' => $item->meta['showParent'] ?? false,
            ];
        })->toArray();
    }
}
