<?php
/**
 * Vptemplate model
 */
namespace Delatbabel\ViewPages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Vptemplate
 *
 * This model class is for the database backed website templates table.
 *
 * ### Example
 *
 * <code>
 * // FIXME
 * </code>
 */
class Vptemplate extends Model
{
    use SoftDeletes;

    protected $fillable = ['key', 'name', 'description', 'is_default', 'content'];

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
