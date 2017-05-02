<?php
namespace App\Config\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Config
 *
 * This model class is for the database backed configuration table.
 */
class Config extends Model
{
    protected $fillable = ['environment', 'group', 'key', 'value', 'type'];

    /**
     * Return the configuration data for a specific environment & group.
     *
     * What this function tries to achieve is to return the configuration
     * for a given environment and group.
     *
     * The data that this function returns is actually a set of key => value
     * pairs for the configuration found within group $group.
     *
     * To get the full configuration you need to call this function for each
     * group returned by fetchAllGroups().
     *
     * @param string $environment
     * @param string $group
     * @return array
     */
    public static function fetchSettings($environment = null, $group = 'config')
    {
        $model = static::where('group', '=', $group);

        // Environment can be null, or must match or use the null wildcard.
        $model->where(function ($query) use ($environment) {
            if (empty($environment)) {
                $query->whereNull('environment');
            } else {
                $query->where('environment', '=', $environment)
                    ->orWhereNull('environment');
            }
        });

        // Order by relevance.
        $model->orderBy(DB::raw('CASE
            WHEN environment IS NOT NULL THEN 1
            ELSE 2
        END'));

        $collection = $model->get();
        return static::normalizeCollection($collection);
    }

    /**
     * Return the exact configuration data for a specific environment & group.
     *
     * This function returns the exact configuration data for a specific
     * environment and group, ignoring any wildcard (NULL) values.
     *
     * The data that this function returns is actually a set of key => value
     * pairs for the configuration found within group $group.
     *
     * To get the full configuration you need to call this function for each
     * group returned by fetchAllGroups().
     *
     * @param string $environment
     * @param string $group
     * @return array
     */
    public static function fetchExactSettings($environment = null, $group = 'config')
    {
        $model = static::where('group', '=', $group);

        // Environment can be null, or must match or use the null wildcard.
        $model->where(function ($query) use ($environment) {
            if (empty($environment)) {
                $query->whereNull('environment');
            } else {
                $query->where('environment', '=', $environment);
            }
        });

        $collection = $model->get();
        return static::normalizeCollection($collection);
    }

    /**
     * Normalize a Collection (a result from model->get())
     *
     * Normalize a Collection (a result from model->get()) to a key
     * => value array, picking only the first result in the array. The
     * above queries will produce a collection where the most relevant
     * results happen before the least relevant results, so we just pick
     * the first key=>value pair found in the collection.
     *
     * @param Collection $collection
     * @return array
     */
    protected static function normalizeCollection(Collection $collection)
    {
        $result = [];

        /** @var Config $item */
        foreach ($collection as $item) {
            if (empty($result[$item->key])) {
                switch (strtolower($item->type)) {
                    case 'string':
                        $result[$item->key] = (string)$item->value;
                        break;
                    case 'integer':
                        $result[$item->key] = (integer)$item->value;
                        break;
                    case 'double':
                        $result[$item->key] = (double)$item->value;
                        break;
                    case 'boolean':
                        $result[$item->key] = (boolean)$item->value;
                        break;
                    case 'array':
                        $result[$item->key] = unserialize($item->value);
                        break;
                    case 'null':
                        $result[$item->key] = null;
                        break;
                    default:
                        $result[$item->key] = $item->value;
                }
            }
        }

        return $result;
    }

    /**
     * Return an array of all groups.
     *
     * @return array
     */
    public static function fetchAllGroups()
    {
        $model = new self;

        $result = [];
        try {
            foreach ($model->select('group')->distinct()->get() as $row) {
                $result[] = $row->group;
            }
        } catch (\Exception $e) {
            // Do nothing.
        }

        return $result;
    }

    /**
     * Store a group of settings into the database.
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string $environment
     * @param string $type   "array"|"string"|"integer"
     * @return Config
     */
    public static function set($key, $value, $group = 'config', $environment = null, $type = 'string')
    {
        //Lets check if we are doing special array handling
        $arrayHandling = false;
        $keyExploded   = explode('.', $key);
        if (count($keyExploded) > 1) {
            $arrayHandling = true;
            $key           = array_shift($keyExploded);
            if ($type == 'array' && ! is_array($value)) {
                $value = unserialize($value);
            }
        }

        // First let's try to fetch the model, if it exists then we need to do an
        // Update not an insert
        $model = static::where('key', '=', $key)->where('group', '=', $group);

        // Environment can be null or must match.
        if (empty($environment)) {
            $model->whereNull('environment');
        } else {
            $model->where('environment', '=', $environment);
        }

        $model = $model->first();

        if (empty($model)) {

            //Check if we need to do special array handling
            if ($arrayHandling) {
                // we are setting a subset of an array
                $array = [];
                self::buildArrayPath($keyExploded, $value, $array);
                $value = serialize($array);
                $type  = 'array';
            }

            if (is_array($value)) {
                $value = serialize($value);
            }

            return static::create(
                [
                    'environment' => $environment,
                    'group'       => $group,
                    'key'         => $key,
                    'value'       => $value,
                    'type'        => $type,
                ]);
        }

        //Check if we need to do special array handling
        if ($arrayHandling) {
            // we are setting a subset of an array
            $array = [];
            self::buildArrayPath($keyExploded, $value, $array);

            //do we need to merge?
            if ($model->type == 'array') {
                $array = array_replace_recursive(unserialize($model->value), $array);
            }
            $value = serialize($array);

            $type = 'array';
        }

        if (is_array($value)) {
            $value = serialize($value);
        }

        $model->value = $value;
        $model->type  = $type;
        $model->save();
        return $model;
    }

    /**
     * This inserts a value into an array at a point in the array path.
     *
     * ### Example
     *
     * <code>
     * $map = [1, 2];
     * $value = 'hello';
     * $array = [];
     *
     * buildArrayPath($map, $value, $array);
     * // $array is now [1 => [2 => 'hello']]
     * </code>
     *
     * @param array $map
     * @param mixed $value
     * @param $array
     * @return void
     */
    protected static function buildArrayPath($map, $value, &$array)
    {
        $key = array_shift($map);
        if (count($map) !== 0) {
            $array[$key] = [];
            self::buildArrayPath($map, $value, $array[$key]);
        } else {
            $array[$key] = $value;
        }
    }

    public static function explodeGroupAndKey($key)
    {
        // Parse the key here into group.key.part components.
        //
        // Any time a . is present in the key we are going to assume the first
        // section is the group. If there is no group present then we assume
        // that the group is "config".
        $explodedOnGroup = explode('.', $key);
        if (count($explodedOnGroup) > 1) {
            $group = array_shift($explodedOnGroup);
            $item  = implode('.', $explodedOnGroup);
        } else {
            $group = 'config';
            $item  = $key;
        }

        return [$group, $item];
    }
}
