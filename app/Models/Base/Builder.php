<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;
use Illuminate\Support\Str;

class Builder extends BaseBuilder
{
    private $functions = [
        'append' => null,
        'sort' => null,
        'order' => null,
        'limit' => null,
        'page' => null,
        'field' => 'filterFields',
        'with' => 'filterWith',
        'without' => 'filterWithout',
    ];

    // Suffix keyed fields as separator
    private $separators = [
        '-notlike' => 'not like',
        '-not' => '<>',
        '-like' => 'like',
        '-more' => '>=',
        '-less' => '<=',
        '-min' => '>',
        '-max' => '<',
        '-between' => 'between',
    ];

    // Prefix keyed fields as cast
    private $casts = [
        'has-' => 'has',
        'date-' => 'date',
    ];

    public function table()
    {
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order') == 'asc' ? 'asc' : 'desc';
        $append = request('append');

        $collection = $this->auth()->filtering()
            ->orderBy($sort, $order)
            ->paginate($limit);
        if ($append) {
            $append = explode(',', $append);
            $collection->append($append);
        }

        return $collection;
    }

    public function filtering()
    {
        $functionKeys = array_keys($this->functions);
        $filterFunctions = collect(request()->only($functionKeys));
        $filterRequests = collect(request()->except($functionKeys));

        // Exec custom function if request is filtered functions
        $this->setFilterFunction($filterFunctions);

        // Skip filter if starts with "_"
        $filterRequests = $filterRequests->filter(function ($value, $key) {
            return $value !== null && ! Str::startsWith($key, '_');
        });
        // Set filtered where request
        $this->setFilterRequest($filterRequests);

        return $this;
    }

    public function setFilterFunction($filter)
    {
        $functions = collect($this->functions);
        $filter->each(function ($val, $key) use ($functions) {
            $functions->each(function ($fn, $name) use ($val, $key) {
                if ($fn !== null && $name == $key) {
                    $this->{$fn}($val);
                }
            });
        });
    }

    public function filterFields($fields)
    {
        $fields = explode(',', $fields);

        return $this->select($fields)->distinct();
    }

    public function setFilterRequest($filter, $query = null)
    {
        if ($query === null) {
            $query = $this;
        }
        $filter->each(function ($val, $key) use ($query) {
            // check if child is string / has no grandchild
            if (is_string($val)) {
                $this->setWhere([$key => $val], $query);
            } else {
                // exclusive where has
                if (strstr($key, 'has-')) {
                    $key = ltrim($key, 'has-');
                    $query->whereHas($key, function ($q) use ($val) {
                        if (is_string(reset($val))) {
                            $q->setWhere($val, $q);
                        }
                    });
                }

                $query->with([$key => function ($q) use ($val) {
                    if (is_string(reset($val))) {
                        $q->setWhere($val, $q);
                    } else {
                        //if has more child run recursively
                        $this->setFilterRequest(collect($val), $q);
                    }
                }]);
            }
        });
    }

    public function setWhere($where, $query)
    {
        foreach ($where as $key => $val) {
            extract($this->getKeySeparator($key));

            $operator = strstr($val, '|') ? '|' : ',';
            foreach (explode($operator, $val) as $id => $val) {
                $val = in_array($separator, ['like', 'not like']) ? "%$val%" : $val;
                if ($id == 0 || $operator == ',') {
                    if ($val == 'null') {
                        //var_dump($key, $val, in_array($separator, ['<>']));
                        if (in_array($separator, ['<>'])) {
                            $query->whereNotNull($key);
                        } else {
                            $query->whereNull($key);
                        }
                    } else {
                        $query->where($key, $separator, $val);
                    }
                } else {
                    if ($val == 'null') {
                        if (in_array($separator, ['<>'])) {
                            $query->orWhereNotNull($key);
                        } else {
                            $query->orWhereNull($key);
                        }
                    } else {
                        $query->orWhere($key, $separator, $val);
                    }
                }
            }
        }
    }

    public function getKeySeparator($key)
    {
        $separators = collect($this->separators);
        $separator = '=';
        $separators->search(function ($separatorVal, $separatorKey) use (&$key, &$separator) {
            if (stristr($key, $separatorKey)) {
                $separator = $separatorVal;
                //$key = str_replace($separatorKey, '', $key);
                $key = substr($key, 0, strlen($key) - strlen($separatorKey));
                //var_dump($key, $separatorKey);
            }
        });

        return compact('key', 'separator');
    }
}
