<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Event\EventInterface;
use Cake\Controller\Controller;
use DateTime;
use Exception;
use phpDocumentor\Reflection\Types\This;
use Google\Client;
use Google\Service\YouTube;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
ob_start();
class AppController extends Controller
{
    public $filter_condition_construct = [];
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        $this->Session = $this->request->getSession();

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }


    public function google_service()
    {
        $this->_google = new Client();
        $this->_google->setApplicationName("Client_Youtube_API");
        $this->_google->setDeveloperKey("AIzaSyDlprhOGAWQwiDa56JJCVfc_MzBfO8_bgA");
        // $this->_google->setDeveloperKey("AIzaSyBnBxFNix2VphH61RHhvKIoQvCQgDFc4f0");
        // $this->_google->setDeveloperKey("AIzaSyBVHFe7V9t27Y6BuN4r5yW4hP5GxLeNcUM");
        // $this->_google->setDeveloperKey("AIzaSyCMBm6F9Iru7vB038KA3jDs-WtS7PZXqX8");
    }


    // 管理側だけのログイン状態をチェック
    public function isAuthorized($user = null)
    {
        // Any registered user can access public functions
        if (!$this->request->getParam('prefix')) {
            return true;
        }

        // Only admins can access admin functions
        if ($this->request->getParam('prefix') === 'Admin') {
            return (bool)($this->isLogin());
        }

        // Default deny
        return false;
    }



    /**
     * 
     * @param Cake\Event\EventInterface $event
     */
    public function beforeFilter(EventInterface $event)
    {
        //端末判定
        $this->request->addDetector(
            'mb',
            [
                'env' => 'HTTP_USER_AGENT',
                'options' => [
                    '^DoCoMo', 'UP\\.Browser', '^SoftBank', '^Vodafone', 'J-PHONE',
                    'NetFront', 'Symbian'
                ]
            ]
        );
        $this->request->addDetector(
            'sp',
            [
                'env' => 'HTTP_USER_AGENT',
                'options' => [
                    'Android.+Mobile', 'iPhone', 'iPod', 'Windows Phone'
                ]
            ]
        );
        $this->set('isMobile', $this->request->is('mb'));
        $this->set('isSp', $this->request->is('sp'));
        $prefix = $this->request->getParam('prefix');
        if ($prefix === 'Admin') {
            //     /** 
            //      * Layout 設定
            //      * your layout file in the path templates/layout/
            //      */
            $this->viewBuilder()->setLayout('admin');
            //     /** 
            //      * Theme 設定
            //      * All file your themed need input the path plugins/Admin/templates/Admin/..
            //      * If want use themed, need create $this->addPlugin('Admin') in function bootstrap()
            //      * of class src\Application
            //      */
            //     // $this->viewBuilder()->setTheme('Admin');
        } else {
            //     /** 
            //      * Layout 設定
            //      * your layout file in the path templates/layout/
            //      */
            $this->viewBuilder()->setLayout('simple');
            //     /** 
            //      * Theme 設定
            //      * All file your themed need input the path plugins/PC/templates/..
            //      * If want use themed, need create $this->addPlugin('PC') in function bootstrap()
            //      * of class src\Application
            //      * 
            //      * Config path of themed in file config/app.php
            //      * App [
            //      *     ...
            //      *     paths [
            //      *         plugins: [
            //      *              'path/of/plugin1'
            //      *              'path/of/plugin1'
            //      *              ...
            //      *         ]
            //      *     ]
            //      * ]
            //      */
            //     // $this->viewBuilder()->setTheme('PC');
        }
        // 準備
        $this->_prepare();
        $this->getAuthComponent();
    }

    private function getAuthComponent()
    {
        $params = $this->request->getAttribute('params');
        if (@$params['prefix'] == 'Admin') {

            $this->loadComponent('Auth', [
                'loginAction' => [
                    'controller' => 'Admins',
                    'action' => 'index',
                ],
                'authError' => 'ログインが必要です。',
                'authenticate' => [
                    'Form' => [
                        'fields' => ['username' => 'login_id', 'password' => 'password'],
                        'finder' => 'auth'
                    ]
                ],
                'storage' => ['className' => 'Session', 'key' => 'Auth.Admin'],
                'authorize' => 'Controller',
            ]);
        }
    }

    private function _prepare()
    {
        // 管理側のログイン制限
        $params = $this->request->getAttribute('params');
        if (@$params['prefix'] == 'Admin') {
            // /app/config/app.php
            $trust = (Configure::read(__('Trust.Admin.login_status.controller.{0}', $params['controller']))) ?? [];
            if (in_array($params['action'], $trust)) return;
            $this->checkLogin();
        }
    }


    /**
     * 追加、編集
     *@param integer $id
     *@param \ArrayObject $option the options to use for the save operation
     * */
    protected function _edit($id = null, $option = [])
    {
        $option = array_merge(
            [
                'saveAll' => false,
                'saveMany' => false,
                'callback' => null,
                'redirect' => ['controller' => $this->modelName, 'action' => 'index']
            ],
            $option
        );
        extract($option);

        $data = $id &&  $this->_detail($id) ? $this->_detail($id) : $this->{$this->modelName}->newEmptyEntity();

        if ($this->request->is(['post', 'put']) && $this->request->getData()) {
            if ($saveMany) {
                $entity = $this->{$this->modelName}->patchEntity($data, $this->request->getData(), ['fields' => $saveMany]);
            } else {
                $entity = $this->{$this->modelName}->patchEntity($data, $this->request->getData());
            }

            if ($this->{$this->modelName}->save($entity)) {
                if ($callback) {
                    $callback($entity);
                }
                if ($redirect) {
                    $this->redirect($redirect);
                }
            } else {
                $this->set('list_errors', $entity->getErrors());
                $this->Flash->set(__('正しく入力されていない項目があります。'));
            }
        }
        $this->set('data', $data);
        return $data;
    }


    /**
     * 追加、編集
     * 
     *@param integer $id
     *@param \ArrayObject $cond
     * */
    protected function _detail($id = null, $cond = [])
    {
        $cond = empty($cond) && !is_null($id) ? ['id' => $id] : $cond;

        if (empty($cond)) return null;

        $mapper = function ($table, $key, $mapReduce) {
            if ($table->attaches)
                $table->attaches = json_decode($table->attaches, true);
            $mapReduce->emit($table, $key);
        };

        $reducer = function ($table, $key, $mapReduce) {
            $mapReduce->emit($table, $key);
        };

        $data = $this->{$this->modelName}
            ->find('all', [
                'conditions' => $cond
            ])
            ->mapReduce($mapper, $reducer)
            ->first();
        $this->set(compact('data'));
        return $data;
    }


    /**
     * 一覧
     * 
     * @param \ArrayObject $cond
     * @param \ArrayObject $options
     * */
    protected function _lists($cond = [], $options = [])
    {
        $primary_key = $this->{$this->modelName}->getPrimaryKey();
        $this->paginate = array_merge(
            [
                'order' => [$this->modelName . '.' . $primary_key . ' DESC'],
                'limit' => 10,
                'paramType' => 'querystring'
            ],
            $options
        );

        $mapper = function ($table, $key, $mapReduce) {
            if ($table->attaches)
                $table->attaches = json_decode($table->attaches, true);
            $mapReduce->emit($table, $key);
        };

        $reducer = function ($table, $key, $mapReduce) {
            $mapReduce->emit($table, $key);
        };

        if ($cond)
            $options['conditions'] = $cond;

        if ($this->paginate['limit'] === null) {
            unset(
                $options['limit'],
                $options['paramType']
            );

            $lists = $this->{$this->modelName}
                ->find('all', $options)
                ->mapReduce($mapper, $reducer);
        } else {
            $lists = $this->{$this->modelName}
                ->find()
                ->mapReduce($mapper, $reducer);
        }

        $assoctiation = $this->{$this->modelName}->associations()->keys();
        if ($assoctiation) {
            $lists = $lists->contain($assoctiation);
        }
        $datas = ($this->paginate['limit'] === null) ? $lists->toArray() : $this->paginate($lists, $options);

        $this->set($this->{$this->modelName}->getTable(), $datas);
        return $datas;
    }

    /**
     * 削除
     * @param integer $id
     * @param string $type
     * @param \ArrayObject $options
     */
    protected function _delete($id, $type, $columns = null, $options = [])
    {
        $option = array_merge(
            ['redirect' => null],
            $options
        );
        extract($option);

        $data = $this->_detail($id);
        if ($data && in_array($type, ['image', 'file', 'content'])) {
            if ($type === 'image' && isset($this->{$this->modelName}->attaches['images'][$columns])) {
                if (isset($data->attaches[$columns])) {
                    foreach ($data->attaches[$columns] as $_) {
                        $str_split = str_split($_);
                        $str_split[0] = ($str_split[0] === DS) ? '' : DS;
                        $_file = new File(WWW_ROOT . implode('', $str_split));
                        if ($_file->exists()) $_file->delete();
                    }
                }
                $data->{$columns} = null;
                $this->{$this->modelName}->save($data);
            } else if ($type === 'file' && isset($this->{$this->modelName}->attaches['files'][$columns])) {
                if (isset($data->attaches[$columns])) {
                    $str_split = str_split($data->attaches[$columns]);
                    $str_split[0] = ($str_split[0] === DS) ? '' : DS;
                    $_file = new File(WWW_ROOT . implode('', $str_split));
                    if ($_file->exists()) $_file->delete();


                    $data->{$columns} = null;
                    $data->{$columns . '_name'} = null;
                    $data->{$columns . '_size'} = null;
                    $this->{$this->modelName}->save($data);
                }
            } else if ($type === 'content') {
                $image_index = array_keys($this->{$this->modelName}->attaches['images']);
                $file_index = array_keys($this->{$this->modelName}->attaches['files']);

                $arr_file = array_merge($image_index, $file_index);
                foreach ($arr_file as $idx) {
                    if (!isset($data->attaches[$idx])) continue;
                    $data->attaches[$idx] = !is_array($data->attaches[$idx]) ? [$data->attaches[$idx]] : $data->attaches[$idx];

                    foreach ($data->attaches[$idx] as $_) {
                        $str_split = str_split($_);
                        $str_split[0] = ($str_split[0] === DS) ? '' : DS;
                        $_file = new File(WWW_ROOT . implode('', $str_split));
                        if ($_file->exists()) $_file->delete();
                    }
                }
                $this->{$this->modelName}->delete($data);
                $id = null;
            }
        }

        if ($redirect !== false) {

            if (is_null($redirect) || $redirect === true) {
                $redirect = $id ? ['action' => 'edit', $id] : ['action' => 'index'];
            }
            $this->redirect($redirect);
        }

        return true;
    }

    /**
     * 順番並び替え
     * @param integer $id
     * @param string $pos
     * @param \ArrayObject $options
     * */
    protected function _position($id, $pos, $options = [])
    {
        $options = array_merge([
            'redirect' => ['action' => 'index', '#' => 'content-' . $id]
        ], $options);
        extract($options);

        if ($this->_detail($id))
            $this->{$this->modelName}->movePosition($id, $pos);

        if ($redirect)
            $this->redirect($redirect);
    }

    /**
     * 掲載中/下書き トグル
     * @param integer $id
     * @param \ArrayObject $options
     * */
    protected function _enable($id, $options = [])
    {
        $options = array_merge([
            'redirect' => ['action' => 'index', '#' => 'content-' . $id]
        ], $options);
        extract($options);

        if ($data = $this->_detail($id)) {
            $status = $data->status != 'publish' ? 'publish' : 'draft';
            $model = $this->{$this->modelName};
            $model->updateAll([$model->aliasField('status') => $status], [$model->aliasField($model->getPrimaryKey()) => $id]);
        }
        if ($redirect) {
            $this->redirect($redirect);
        }
    }


    /**
     * ログインしている状態
     * */
    public function isLogin()
    {
        return $this->Session->check('Auth.Admin');
    }


    /**
     * ログイン状態をチェック
     * */
    public function checkLogin()
    {
        if (!$this->isLogin()) {
            $this->redirect('/admin');
        }
    }


    /**
     * GBiz CURL
     */

    protected function curl_Gbiz($code = null, $token = null)
    {
        if (is_null($code) && is_null($token)) $this->redirect('/');

        $url = __('{base_url}/oauth/userinfo', ['base_url' => API_BASE_URL]);
        $header = [__('Authorization: Bearer {token}', ['token' => $token])];

        $curlOpt = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ];

        if ($code && $token === null) {
            $curlOpt[CURLOPT_URL] = __('{base_url}/oauth/token', ['base_url' => API_BASE_URL]);
            $curlOpt[CURLOPT_HTTPHEADER] = [__('Authorization: Basic {base64}', ['base64' => base64_encode(__('{0}:{1}', API_CLIENTID, CLIENT_SECRET))])];
            $curlOpt[CURLOPT_POST] = 1;
            $curlOpt[CURLOPT_POSTFIELDS] = http_build_query(['grant_type' => 'authorization_code', 'code' => $code], '', '&');
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curlOpt);
        $rs = curl_exec($ch);
        curl_close($ch);

        return (array)json_decode($rs, true);
    }

    /**
     * URLのパラメータからQuery条件を立てる
     * @param bool $filter 検索フラグ。Method Get。一蘭画面よく使う検索機能
     * @param \ArrayObject $options 他の条件が含まれる
     */
    public function build_conditions($filter = false, $options = [])
    {
        $cond = [];
        $replace_character = ['-', ' ', ':', '/'];
        $this_schema = $this->{$this->modelName}->getSchema();
        $query_param = $this->request->getQueryParams();

        if ($filter && !empty($query_param)) {

            $this->set(array_keys($query_param), $query_param);
            $this->set('query_param', $query_param);
            extract($query_param);

            foreach ($this->filter_condition_construct as $col => $v) {
                $column_default = __('{0}.{1}', $this->modelName, $col);
                $schema = $this_schema;

                if (strpos($col, '.') !== false) {
                    $column_default = $col;
                    $explode_col = explode('.', $col);
                    $schema = $this->fetchTable($explode_col[0])->getSchema();
                    $col = $explode_col[1];
                }

                $column = $column_default;
                $value = $v;
                if (is_array($v)) {
                    $val = array_keys($v);
                    $interconnect = $val[0];
                    $column = __('{0}{1}', $column, $interconnect);
                    $value = $v[$interconnect];

                    // とりあえずdatetimeタイプ
                    if (isset($v['format'])) {
                        switch (trim($interconnect)) {
                            case 'between':
                                $column = __('DATE_FORMAT({0},"%{1}"){2}', $column_default, str_replace($replace_character, '%', $v['format']), '{0}');
                                if (!isset($start) && !isset($end)) break;
                                try {
                                    $start_dt = new DateTime($start);
                                    $end_dt = new DateTime($end);

                                    $cond[__($column, ' >=')] = $start_dt->format(str_replace($replace_character, '', $v['format']));
                                    $cond[__($column, ' <=')] = $end_dt->format(str_replace($replace_character, '', $v['format']));
                                } catch (Exception $e) {
                                }
                                break;

                            default:
                                if (!isset($$col) || trim($$col) === '') break;
                                try {
                                    $col_dt = new DateTime($$col);
                                    $$col = $col_dt->format(str_replace($replace_character, '', $v['format']));
                                    $interconnect = ($interconnect !== $schema->getColumnType($col)) ? $interconnect : '';
                                    $column = __('DATE_FORMAT({0},"%{1}"){2}', $column_default, str_replace($replace_character, '%', $v['format']), $interconnect);
                                } catch (Exception $e) {
                                }
                                break;
                        }
                    }
                }
                if (!isset($$col) || trim($$col) === '') continue;
                $cond[$column] = __($value, [$col => $$col]);
            }
        }
        // dd($cond);
        return array_merge($options, $cond);
    }

    // sort has pagination
    protected function _sort_pagination()
    {
        if ($this->request->is(['post', 'put']) && $this->request->data) {
            if (!empty($this->request->data[$this->modelName])) {
                $post = $this->request->data[$this->modelName];
                $arr_key_of_post = array_keys($post);
                asort($arr_key_of_post);
                $array_flip = array_flip($arr_key_of_post);

                $arr_key_of_flip  = array_keys($array_flip);
                $datas = [];
                foreach ($arr_key_of_flip as $k => $pos) {
                    $n = $post[$arr_key_of_post[$k]];
                    $n['position'] = $pos;
                    $datas[] = $n;
                }

                if (count($datas) > 0) {
                    $r = $this->{$this->modelName}->saveAll($datas, ['validate' => false, 'deep' => false]);
                    if (!$r) {
                        header('Content-Type: text/plain; charset=UTF-8');
                        die('何らかの理由で並び替えの保存に失敗しました。');
                    }
                }
            }
        }
    }

    /**
     * 時間の計算
     * 何〇〇前
     * 
     * echo time_elapsed_string('2013-05-01 00:22:35');
     * echo time_elapsed_string('@1367367755'); # timestamp input
     * echo time_elapsed_string('2013-05-01 00:22:35', true);
     * 
     * OUT PUT
     * 4 months ago
     * 4 months ago
     * 4 months, 2 weeks, 3 days, 1 hour, 49 minutes, 15 seconds ago
     */
    function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $now->setTimezone(new \DateTimeZone('Asia/Tokyo'));

        $ago = new DateTime($datetime);
        $ago->setTimezone(new \DateTimeZone('Asia/Tokyo'));
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => '年',
            'm' => '月',
            'w' => '週',
            'd' => '日',
            'h' => '時',
            'i' => '分',
            's' => '秒',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . '' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . '前' : '数秒前';
    }
}
