<?php
/**
 * @author ciogao@gmail.com
 *         Class AlbumController
 */
class AlbumController extends Controller
{

    /**
     * @var models_album
     */
    private $model_album = NULL;

    /**
     * @var models_user
     */
    private $model_user = NULL;

    /**
     * @var models_collect
     */
    private $model_collect = NULL;

    /**
     * @var models_albumListen
     */
    private $models_albumListen = NULL;

    public function init()
    {
        parent::init();
        $this->model_album = models_album::getInstance();
        $this->model_user  = models_user::getInstance();
    }

    /**
     * 创建相册
     */
    public function createAction()
    {
        $this->rest->method('POST');

        $params                    = $this->getRequest()->getPost();
        $this->rest->paramsMustMap = array('album_name', 'is_open');
        $this->rest->paramsMustValid($params);

        if ($this->model_album->exits(array('album_name' => $params['album_name'], 'user_id' => $this->userinfo['user_id']))) {
            $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR_DB_REPEAT, '相册已存在');
        }

        $params['user_id'] = $this->userinfo['user_id'];

        $data   = $this->model_album->mkdata($params);
        $result = $this->model_album->insert($data);

        $this->model_user->addalbum();

        if ($result == FALSE) $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR);

        $this->rest->success('', rest_Code::STATUS_SUCCESS, '创建成功');
    }

    /**
     * 我的相册
     */
    public function mineAction()
    {
        $this->rest->method('GET');
        $data = $this->model_album->myAlbum(1);

        if (!is_array($data) || count($data) < 1) {
            $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR_DB_NULL);
        }

        $start = $this->getRequest()->getParam('start',0);
        $limit = $this->getRequest()->getParam('limit',contast_album::PAGE_SIZE_DEFAULT);

        $this->mkData->setOffset($start, $limit);
        $this->mkData->config(count($data), 'album_id');
        $data = $this->mkData->make($data);

        $this->rest->success($data);
    }

    /**
     * 收藏图片到相册
     */
    public function collectionAction()
    {
        $this->check->method('POST');
        $params                    = $this->getRequest()->getPost();
        $this->check->paramsMustMap = array('items_id', 'album_id');
        $this->check->paramsMustValid($params);

        if ((int)$this->getRequest()->getPost('album_id') < 1) {
            $this->rest->error(rest_Code::STATUS_ERROR_PARAMS);
        }

        $this->model_collect = models_collect::getInstance();
        if ($this->model_collect->exits(array('album_id' => $params['album_id'], 'user_id' => $this->userinfo['user_id'], 'items_id' => $params['items_id']))) {
            $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR_DB_REPEAT, '重复操作');
        }

        $params['user_id'] = $this->userinfo['user_id'];

        $data   = $this->model_collect->mkdata($params);
        $result = $this->model_collect->insert($data);

        $this->model_user->addcollect();
        if ($result == FALSE) $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR);

        $this->rest->success('', rest_Code::STATUS_SUCCESS, '收藏成功');
    }

    /**
     * 订阅图集
     */
    public function listenAction()
    {
        $this->rest->method('POST');
        $params                    = $this->getRequest()->getPost();
        $this->rest->paramsMustMap = array('album_id');
        $this->rest->paramsMustValid($params);

        if ((int)$this->getRequest()->getPost('album_id') < 1) {
            $this->rest->error(rest_Code::STATUS_ERROR_PARAMS);
        }

        $this->models_albumListen = models_albumListen::getInstance();
        if ($this->models_albumListen->exits(array('album_id' => $params['album_id'], 'user_id' => $this->userinfo['user_id']))) {
            $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR_DB_REPEAT, '请误重复订阅');
        }

        $params['user_id'] = $this->userinfo['user_id'];

        $data   = $this->models_albumListen->mkdata($params);
        $result = $this->models_albumListen->insert($data);

        if ($result == FALSE) $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR);

        $this->rest->success('', rest_Code::STATUS_SUCCESS, '订阅成功');
    }

    /**
     * 查看我的订阅
     */
    public function listenedAction()
    {
        $this->models_albumListen = models_albumListen::getInstance();
        $albums = $this->models_albumListen->myListenedAlbum();

        if (!is_array($albums) || count($albums) < 1){
            $this->rest->error(rest_Code::STATUS_SUCCESS_DO_ERROR_DB_NULL);
        }

        $this->rest->success($albums);
    }

    /**
     * 查看某个相册
     */
    public function showAction()
    {

    }
}