<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/10/17
 * Time: 14:20
 */

namespace app\controllers;


use app\models\Movie;
use yii\rest\ActiveController;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\auth\HttpBearerAuth;
use Yii;
use yii\web\ServerErrorHttpException;

class MovieController extends ActiveController
{
    public $modelClass = 'app\models\Movie';

    #Método que diz que o retorno deve ser com páginação e que os valores são listados dentro de {items}
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];


    /**
     * @param string $action
     * @param null $model
     * @param array $params
     * Controle de permissão de usuário
     * Aqui poderiamos bloquear um usuário/aplicação consumidor da api de acessar um determinado endpoint
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'delete' && \Yii::$app->user->id === 2) {
            throw new \yii\web\ForbiddenHttpException(sprintf('Você não tem permissão para deletar.', $action));
        }

    }

    public function beforeAction($action)
    {
        //Verifica se usuário autenticou. Autenticação feita em User->findIdentityByAccessToken
        if(\Yii::$app->user->identity)
        {

            return true;
        }


        return parent::beforeAction($action);
    }

    /*
     * Método de Autenticação HttpBasicAuth
     */
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        #AUTENTICAÇÃO
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];

        #CORS
        // 1) remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // 2) add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['http://localhost:8080'],
                //'Access-Control-Request-Method' => ['GET', 'POST', 'DELETE', 'HEAD', 'OPTIONS','PUT'],
                //'Access-Control-Request-Headers' => '',
                //'Access-Control-Max-Age' => '',
                //'Access-Control-Allow-Credentials' => '',

                //Usei no Silex
                //$response->headers->set('Access-Control-Allow-Origin', '*');
                //$response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
                //$response->headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type');
                //$response->headers->set('Content-Type', 'application/json');//Header de resposta sempre application/json
            ],
        ];

        // 3) re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // 4) avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];



        return $behaviors;
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     * Mostra informações de um filme
     */
    public function actionViewApp($id){

        $model = Movie::find()
            //->select(['id','title'])Outra forma de limitar os parametros retornados
            ->where(['id' => $id])
            ->with('actors')
            ->asArray()
            ->one();

        if($model !== null){
            return $model;
        } else {
            throw new NotFoundHttpException('Filme não encontrado');
        }

    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     * @throws NotFoundHttpException
     * Lista todos os Filmes com paginação
     */
    public function actionIndexApp(){


        $model = Movie::find()
            //->select(['id','title'])Outra forma de limitar os parametros retornados
            //->where(['id' => $id])
            ->with('actors')
            ->asArray()
            ->all();

        if($model !== null){
            return $model;
        } else {
            throw new NotFoundHttpException('Nenhum filme não encontrado');
        }

    }

    /**
     * @return array|string
     * form-data Ex: title => Nome do Filme
     */
    public function actionCreateApp(){

        $movie = new Movie();
        $movie->setAttributes(Yii::$app->request->post(), false);
        //return Yii::$app->request->headers;
        //return $_POST['title'];
        //return $model->attributes;

        //Validate
        if(!$movie->validate()){
            Yii::$app->response->statusCode = 422;
            Yii::$app->response->statusText = 'Data Validation Failed.';
            return $movie->errors;
        }

        //SAVE
        if(!$movie->save()){
            Yii::$app->response->statusCode = 500;
            throw new ServerErrorHttpException('Tivemos um erro. Tente novamente mais tarde');
        } else {
            Yii::$app->response->statusCode = 201;
            return $movie->attributes;
        }

    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdateApp($id){

        $movie = Movie::findOne($id);

        if($movie !== null){

            $movie->setAttributes(Yii::$app->request->post(), false);
            //return Yii::$app->request->post();
            //Validate
            if(!$movie->validate()){
                Yii::$app->response->statusCode = 422;
                Yii::$app->response->statusText = 'Data Validation Failed.';
                return $movie->errors;
            }

            //SAVE
            if(!$movie->save()){
                Yii::$app->response->statusCode = 500;
                throw new ServerErrorHttpException('Tivemos um erro. Tente novamente mais tarde');
            } else {
                Yii::$app->response->statusCode = 201;
                return $movie->attributes;
            }
        }
        throw new NotFoundHttpException('Nenhum filme não encontrado');



    }

    /**
     * @param $id
     * @return bool
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     *
     */
    public function actionDeleteApp($id){
        $this->checkAccess('delete');
        $movie = Movie::findOne($id);

        if($movie !== null){
            $delete = $movie->delete();
            if($delete){
                Yii::$app->response->statusCode = 201;
                return true;
            } else {
                Yii::$app->response->statusCode = 500;
                throw new ServerErrorHttpException('Tivemos um erro. Tente novamente mais tarde');
            }
        } else {
            throw new NotFoundHttpException('Nenhum filme não encontrado');
        }

    }

}