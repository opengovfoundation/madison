<?php

namespace App\Helpers;

use App\Models\Category;

class CategoryHelpers
{
    /**
     * Returns the current URL with the provided category ID added to the
     * query string.
     */
    public static function urlPlusCategory($request, $categoryId)
    {
        $categories = $request->query('categories') ? $request->query('categories') : [];

        array_filter($categories);

        if (!in_array($categoryId, $categories)) {
            array_push($categories, $categoryId);
        }

        return $request->fullUrlWithQuery([
            'categories' => $categories
        ]);
    }


    /**
     * Returns the current URL with the provided category ID removed from the
     * query string.
     */
    public static function urlMinusCategory($request, $categoryId)
    {
        $categories = $request->query('categories') ? $request->query('categories') : [];

        $idIndex = array_search($categoryId, $categories);
        unset($categories[$idIndex]);

        return $request->fullUrlWithQuery([
            'categories' => $categories
        ]);
    }

}
