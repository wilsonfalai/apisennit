<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "movie".
 *
 * @property int $id
 * @property string $title
 * * @property int $year
 */
class Movie extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'movie';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','year'], 'required'],
            [['title'], 'string', 'max' => 45],
            [['year'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'year' => 'Year',
        ];
    }

    /**
     * @return array
     * Aqui podemos configurar o retorno
     * Podemos formatar o nome do atributo, não retornar um atributo específico ou formatar o valor retornado
     */
    public function fields()
    {
        $fields = [
            'id',
            'movie_title' => 'title',
            'year' => function ($model) {
                return $model->year;
            },
        ];


        return $fields;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMovieHasActors()
    {
        return $this->hasMany(MovieHasActor::className(), ['movie_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActors()
    {
        return $this->hasMany(Actor::className(), ['id' => 'actor_id'])
            //->via('movieHasActors');//Dessa forma retorna também movieHasActor
            ->viaTable('movie_has_actor', ['movie_id' => 'id']);
    }
}
