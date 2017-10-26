<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "movie_has_actor".
 *
 * @property int $id
 * @property int $actor_id
 * @property int $movie_id
 *
 * @property Actor $actor
 * @property Movie $movie
 */
class MovieHasActor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'movie_has_actor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['actor_id', 'movie_id'], 'required'],
            [['actor_id', 'movie_id'], 'integer'],
            [['actor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Actor::className(), 'targetAttribute' => ['actor_id' => 'id']],
            [['movie_id'], 'exist', 'skipOnError' => true, 'targetClass' => Movie::className(), 'targetAttribute' => ['movie_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'actor_id' => 'Actor ID',
            'movie_id' => 'Movie ID',
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'movie_id' => function ($model) {
                return $model->movie->title;
            },
            'actor_id' => function ($model) {
                return $model->actor->name;
            },
        ];


        return $fields;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActor()
    {
        return $this->hasOne(Actor::className(), ['id' => 'actor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMovie()
    {
        return $this->hasOne(Movie::className(), ['id' => 'movie_id']);
    }
}
