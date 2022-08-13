<?php

namespace App\Traits;

trait Filter
{
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

    private function filterRelation($builder, $field)
    {
        var_dump($field);
    }

    /**
     * add filtering.
     *
     * @param    $builder: query builder.
     * @param    $filters: array of filters.
     * @return $query builder.
     */
    public static function scopeFilter($builder, $filters = null)
    {
        $self = new static;
        if (! $filters) {
            $filters = request('filter', []);
        }
        collect($filters)->each(function ($field) use ($self, $builder) {
            if (is_array($field)) { // has relation
                $self->filterRelation($builder, $field);
            }
            // var_dump($field);
        });
        // var_dump($filters,$self->fillable);
        // var_dump($self->getTable());
        return $builder;
        // if(!$filters) {
      //     return $builder;
        // }
        // $tableName = $this->getTable();
        // $defaultFillableFields = $this->fillable;
        // foreach ($filters as $field => $value) {
      //     if(in_array($field, $this->boolFilterFields) && $value != null) {
      //         $builder->where($field, (bool)$value);
      //         continue;
      //     }
      //     if(!in_array($field, $defaultFillableFields) || !$value) {
      //         continue;
      //     }
      //     if(in_array($field, $this->likeFilterFields)) {
      //         $builder->where($tableName.'.'.$field, 'LIKE', "%$value%");
      //     } else if(is_array($value)) {
      //         $builder->whereIn($field, $value);
      //     } else {
      //         $builder->where($field, $value);
      //     }
        // }
        // return $builder;
    }

    public static function scopeTable($builder, $limit = null)
    {
        if ($limit == null) {
            $limit = request('limit', 10);
        }
        $sort = request('sort', 'created_at');
        $order = request('order') == 'asc' ? 'asc' : 'desc';

        return $builder->orderBy($sort, $order)->paginate($limit);
    }
}
