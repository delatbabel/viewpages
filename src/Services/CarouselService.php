<?php

namespace Delatbabel\ViewPages\Services;

use Delatbabel\ViewPages\Models\Carousel;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class CarouselService
 *
 * This is the Carousel service that handles all of the Carousel functionality for displaying as injection in a view
 */
class CarouselService
{
    /**
     * Get and display the first active carousel according to the $key variable
     *
     * @param $key string
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function make($key)
    {
        $user = Sentinel::check();
        $view = 'blocks.' . $key;

        // Set the user specific query parameters
        // for_logged in: 0 == non-members only, 1 == members only, 2 == OK for everyone
        /** @var Builder $carousel_query */
        if ($user) {
            // left join carousel_user pivot table
            // if there is no entry then user_id will be null, which is fine
            // if there is an entry and display_days is not null then check that this carousel falls within display_days
            $carousel_query = Carousel::whereIn('for_logged_in', [1, 2]);
            $carousel_query->leftJoin('carousel_user', function ($join) use ($user) {
                $join->on('carousels.id', '=', 'carousel_user.carousel_id');
                $join->where('carousel_user.user_id', '=', $user->id);
            });
            $carousel_query->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('carousels.display_days');
                    $query->orWhereRaw('CURDATE() <= DATE_ADD(carousel_user.created_at, INTERVAL carousels.display_days DAY)');
                });
                $query->orWhereNull('carousel_user.user_id');
            });
        } else {
            $carousel_query = Carousel::whereIn('for_logged_in', [0, 2]);
        }

        // Set the common query parameters
        // Order by is by carousel end date ascending (newest end dates first) but with NULLs last
        // if there is a tie on end date then go by carousels.updated_at -- most recently updated carousel wins
        $carousel_query->where('key', $key)
            ->where(function ($query) {
                $query->where('start_date', '<=', Carbon::today())
                    ->orWhereNull('start_date');
            })
            ->where(function ($query) {
                $query->where('end_date', '>=', Carbon::today())
                    ->orWhereNull('end_date');
            })
            ->where('status', '=', 'active')
            ->select('carousels.*')
            ->orderByRaw('CASE WHEN carousels.end_date IS NULL THEN 1 ELSE 0 END, carousels.end_date ASC, carousels.updated_at DESC');

        // Debug
        Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
            'Carousel query = ' . $carousel_query->toSql());

        // Debug output
        // Delatbabel\ViewPages\Services\CarouselService::
        // /var/www/socialdiary/app/Modules/Carousel/Services/CarouselService.php:70:make:
        //
        // Carousel query =
        // select `carousels`.*
        //     from `carousels`
        //     where `for_logged_in` in (?, ?)
        //     and `key` = ?
        //     and (`start_date` <= ? or `start_date` is null)
        //     and (`end_date` >= ? or `end_date` is null)
        //     and `status` = ?
        //     order by
        //         CASE WHEN carousels.end_date IS NULL THEN 1 ELSE 0 END,
        //         carousels.end_date ASC,
        //         carousels.updated_at DESC
        //
        // Carousel query =
        // select `carousels`.*
        //     from `carousels`
        //     left join `carousel_user` on `carousels`.`id` = `carousel_user`.`carousel_id`
        //         and `carousel_user`.`user_id` = ?
        //     where `for_logged_in` in (?, ?)
        //     and (
        //         (`carousels`.`display_days` is null or CURDATE() <= DATE_ADD(carousel_user.created_at, INTERVAL carousels.display_days DAY))
        //         or `carousel_user`.`user_id` is null)
        //     and `key` = ?
        //     and (`start_date` <= ? or `start_date` is null)
        //     and (`end_date` >= ? or `end_date` is null)
        //     and `status` = ?
        //     order by
        //         CASE WHEN carousels.end_date IS NULL THEN 1 ELSE 0 END,
        //         carousels.end_date ASC,
        //         carousels.updated_at DESC

        // Fetch the winning carousel by the common query parameters
        /** @var Carousel $carousel */
        $carousel = $carousel_query->first();
        if (! empty($carousel)) {
            Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
                'Found carousel ID ' . $carousel->id);
        } else {
            Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
                'Did not find carousel matching ' . $key);
        }

        // Update the carousel_user join table with a record that shows this has been viewed.
        if (! empty($carousel) && ! empty($user)) {
            try {
                $carousel->users()->attach($user->id);
            } catch (\Exception $e) {
                // NO-OP
            }
        }

        return view($view, ['carousel' => $carousel])->render();
    }

    /**
     * getter method
     */
    public function __get($key)
    {
        return $this->make($key);
    }
}
