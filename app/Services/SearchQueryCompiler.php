<?php

namespace App\Services;

class SearchQueryCompiler
{
    public static function compile($search, $wildcard = true)
    {
        preg_match_all('/[+-~<>|]*(?:".+"|\(.+\))|[^\s]+/', $search, $terms);
        $terms = collect($terms[0]);

        $terms->transform(function ($term) use ($wildcard) {
            if (starts_with($term, '|')) {
                // drop it at this point, space is the OR operator
                $term = substr($term, 1);
            } elseif (starts_with($term, ['+', '-', '~', '<', '>', '@'])) {
                // don't do anything, already has an operator
            } else {
                $term = '+'.$term;
            }

            if ($wildcard) {
                // do not want to wildcard grouped things, it's invalid
                if (ends_with($term, ['"', ')']) || starts_with($term, '@')) {
                    return $term;
                }

                return $term . '*';
            }
        });

        return $terms->implode(' ');
    }
}
