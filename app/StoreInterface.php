<?php
/**
 * Created by PhpStorm.
 * User: Hyder Bangash
 * Date: 1/8/22
 * Time: 1:25 PM
 */

namespace App;


interface StoreInterface
{
    /**
     * Find detail on a specific App
     * @return mixed
     */
    public function app();

    /**
     * Fetch reviews on a specific App
     * @return mixed
     */
    public function reviews();

    /**
     * Fetch App ratings
     * @return mixed
     */
    public function ratings();

    /**
     * Search for an App on store provider
     * @return mixed
     */
    public function search();

}
