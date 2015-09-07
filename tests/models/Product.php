<?php namespace tests\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Nqxcode\LuceneSearch\Model\Searchable;
use Nqxcode\LuceneSearch\Model\SearchTrait;

/**
 * Class Product
 * @property string $name
 * @property string $description
 * @property boolean $publish
 * @method Builder wherePublish
 * @package tests\models
 */
class Product extends Model implements Searchable
{
    use SearchTrait;

    /**
     * @inheritdoc
     */
    public function isSearchable()
    {
        return $this->publish;
    }

    public function getOptionalAttributesAttribute()
    {
        return [
            'custom_text' => 'some custom text',
            'boosted_name' => ['boost' => 0.9, 'value' => $this->name],
        ];
    }
}
