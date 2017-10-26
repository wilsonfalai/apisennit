<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "actor".
 *
 * @property int $id
 * @property string $name
 * @property int $age
 *
 * @property MovieHasActor[] $movieHasActors
 */
class Actor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'actor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'age'], 'required'],
            [['age'], 'integer'],
            [['name'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'age' => 'Age',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMovieHasActors()
    {
        return $this->hasMany(MovieHasActor::className(), ['actor_id' => 'id']);
    }
}
