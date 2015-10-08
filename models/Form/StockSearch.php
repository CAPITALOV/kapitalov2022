<?php

namespace app\models\Form;

use app\models\Form\Stock;
use app\models\User;
use cs\services\Str;
use cs\services\VarDumper;
use Yii;
use yii\base\Model;
use cs\Widget\FileUpload2\FileUpload;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Html;

/**
 * ContactForm is the model behind the contact form.
 */
class StockSearch extends Stock
{
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['name',], 'default'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return self::scenarios();
    }

    public function search($params)
    {
        $query = \app\models\Stock::query();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
