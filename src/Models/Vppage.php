<?php
/**
 * Vppage model
 */
namespace Delatbabel\ViewPages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Vppage
 *
 * This model class is for the database backed website templates table.
 *
 * ### Example
 *
 * <code>
 * // FIXME
 * </code>
 */
class Vppage extends Model
{
    use SoftDeletes;

    protected $fillable = ['key', 'name', 'url', 'description',
        'vptemplate_id', 'is_secure', 'is_homepage', 'content'];

    protected $dates = ['deleted_at'];

    /**
     * 1:Many relationship with Vppage model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function configs()
    {
        return $this->hasMany('Delatbabel\ViewPages\Models\Vppage');
    }
}
