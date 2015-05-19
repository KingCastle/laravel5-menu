<?php namespace Complay\Menu;

use Illuminate\Database\Eloquent\Model;

class Navigation extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'menus';
    protected $fillable = ['name', 'object'];
    protected static $rules = [
        'name' => 'required',
    ];

    public function validate() {
        self::$rules = array_merge_recursive(self::$rules, $this->getUniqueNameRule());
        return parent::validate();
    }

    protected function getUniqueNameRule() {
        return ['name' => 'unique:menus,name,' . (isset($this->id) ? $this->id : 0)];
    }

    public function getObjectAttribute($value) {
        return unserialize(gzuncompress($value));
    }

    public function setObjectAttribute($value) {
        $this->attributes['object'] = @gzcompress(@serialize($value), 9);
    }

}
