<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AppSiteSearchController extends Controller
{
    const BUFFER = 20;  // 20 characters: to show 20 neighbouring characters around the searched word

    /**
     * Here we list down all the alternative model-route mappings
     * These models must have a policy in the AdminServiceProvider
     * Model - ?route (optional)
     */
    private function mapping(): Collection
    {
        $map = [
            Admin::class => 'admins.show',
            Task::class => 'tasks.show',
        ];

        return collect($map);
    }

    public function __invoke(Request $request): JsonResponse
    {
        $keyword = $request->get('keyword');

        $results = $this->mapping()->map(function ($route, $classname) use ($keyword) {

            // For each class, call the search function
            /** @var Model $model */
            $model = app($classname);

            if (Gate::denies('view', $model)) {
                return null;
            }

            // to create the `match` attribute, we need to join the value of all the searchable fields in
            // our model, ie all the fields defined in our 'toSearchableArray' model method

            return $model::search($keyword)
                ->take(5)
                ->get()
                ->map(function ($modelRecord) use ($model, $keyword, $classname, $route) {

                    // We make use of the SEARCHABLE_FIELDS constant in our model
                    // we dont want id in the match, so we filter it out.
                    $fields = array_filter($model::SEARCHABLE_FIELDS, fn ($field) => $field !== 'id');

                    // only extracting the relevant fields from our model
                    $fieldsData = $modelRecord->only($fields);

                    // ex: title -> name
                    $this->renameMainFieldToName($fieldsData);

                    // joining the fields together
                    $serializedValues = Str::ascii(collect($fieldsData)->join(' - '));

                    // finding the position of match
                    $searchPos = strpos(strtolower($serializedValues), strtolower($keyword));

                    // Our goal here:
                    // After finding the match position, we also want to include the surrounding text, so our user would
                    // have a better search experience.
                    //
                    // We append or prepend `...` if there are more text before / after our match + neighbouring text
                    // including the found terms
                    if ($searchPos !== false) {

                        // the buffer number dictqates how many neighbouring characters to display
                        $start = $searchPos - self::BUFFER;

                        // we don't want to go below 0 as the starting position
                        $start = $start < 0 ? 0 : $start;

                        // multiply 2 buffer to cover the text before and after the match
                        $length = strlen($keyword) + 2 * self::BUFFER;

                        // getting the match and neighbouring text
                        $sliced = substr($serializedValues, $start, $length);

                        // adding prefix and postfix dots

                        // if start position is 0, there is no need to prepend `...`
                        $shouldAddPrefix = $start > 0;
                        // if end position went over the total length, there is no need to append `...`
                        $shouldAddPostfix = ($start + $length) < strlen($serializedValues);

                        $sliced = $shouldAddPrefix ? '...'.$sliced : $sliced;
                        $sliced = $shouldAddPostfix ? $sliced.'...' : $sliced;
                    }

                    return [
                        'id' => $modelRecord->id,
                        'name' => $modelRecord->name,
                        'match' => $sliced ?? substr($serializedValues, 0, 20).'...',
                        'model' => $classname,
                        'route' => $this->resolveModelRoute($modelRecord, $route),
                    ];
                });
        })
            ->filter()
            ->flatten(1)
            ->groupBy('model')
            ->map(function (Collection $results, $model) {
                // Prepend Grouped by model name with a disabled field
                return $results
                    ->prepend([
                        'divider' => true,
                    ])
                    ->prepend([
                        'name' => trans(Str::afterLast($model, '\\')),
                        'disabled' => true,
                    ]);
            })
            ->flatten(1);

        return response()->json($results);
    }

    private function resolveModelRoute(Model $model, $route = null)
    {
        if ($route) {
            return route($route, $model->id);
        }

        // getting the Fully Qualified Class Name of model
        $modelClass = get_class($model);

        // converting model name to kebab case
        $modelName = Str::plural(Str::afterLast($modelClass, '\\'));
        $modelName = Str::kebab(Str::camel($modelName));

        // assume /{model-name}/{model_id}
        return URL::to($modelName.'/'.$model->id);
    }

    private function renameMainFieldToName(&$fieldsData): void
    {
        // Blog's main field
        if (isset($fieldsData['title'])) {
            $fieldsData['name'] = $fieldsData['title'];
            unset($fieldsData['title']);
        }
    }
}
