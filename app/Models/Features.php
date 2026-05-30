<?php
namespace RealEstate\Models;

use SkillDo\Database\Eloquent\Model;
use SkillDo\Traits\Eloquent\ModelLanguage;

class Features extends Model
{
    use ModelLanguage;

    protected string $table = 'features';

    protected string $primaryKey = 'id';

    public function __construct($attributes = [])
    {
        $this->language = 'features';

        parent::__construct($attributes);
    }
}

