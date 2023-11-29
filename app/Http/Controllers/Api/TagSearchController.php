<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TagSearchController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $locale, string|null $type = null)
    {
        $search = $request->get('search');

        if (! $search) {
            return [];
        }

        return Tag::query()
            ->containing($search)
            ->withLocale($locale)
            ->when($type, function (Builder $q) use ($type) {
                $q->withType($type);
            })
            ->get()
            ->map(function (Tag $tag) {
                return $tag->name;
            })
            ->toArray();
    }
}
