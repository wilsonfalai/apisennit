<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/10/17
 * Time: 13:59
 */

namespace app\controllers;

use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';
}