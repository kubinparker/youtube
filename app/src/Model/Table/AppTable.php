<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\Filesystem\Folder;
use Cake\ORM\Table;
use Cake\Core\App;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Utility\Text;
use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\I18n\I18n;


// use Cake\Event\EventInterface;

class AppTable extends Table
{
    /**
     * Upload Directory, file convert configure
     * Upload dir is UPLOAD_BASE_URL and UPLOAD_DIR constant at paths.php
     */
    public $uploadDirCreate = true;
    public $uploadDirMask = 0777;
    public $uploadFileMask = 0666;

    public $path_images = '';
    public $path_file = '';

    /**
     * ImageMagick configure
     */
    public $convertPath = '/usr/bin/convert';
    public $convertParams = '-thumbnail';


    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Position');


        $image_path = App::path('uploads.images');
        $file_path = App::path('uploads.files');

        $this->path_images = $image_path[0] . $this->getTable() . DS;
        $this->path_file = $file_path[0] . $this->getTable() . DS;
    }

    /**
     * Custom type of image, file column in table is file type
     *
     * @param Cake\Database\Schema\TableSchemaInterface $schema The configuration for the Table.
     * @return Cake\Database\Schema\TableSchemaInterface
     */
    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        if (isset($this->attaches['images']) && $this->attaches['images']) {
            foreach ($this->attaches['images'] as $column => $_) {
                $schema->setColumnType($column, 'file');
            }
        }
        if (isset($this->attaches['files']) && $this->attaches['files']) {
            foreach ($this->attaches['files'] as $column => $_) {
                $schema->setColumnType($column, 'file');
            }
        }

        return $schema;
    }


    /**
     * Upload Directory, File convert configure
     * Upload dir is UPLOAD_BASE_URL and UPLOAD_DIR constant at paths.php
     */
    public function trustList()
    {
        $schema = $this->getSchema()->columns();
        $trust = [];
        if ($this->blackList) {
            $trust = array_diff($schema, $this->blackList);
        } else {
            $black_list = [];
            if (!empty($this->attaches['images'])) {
                $black_list = $black_list + array_keys($this->attaches['images']);
            }
            if (!empty($this->attaches['files'])) {
                foreach ($this->attaches['files'] as $key => $_) {
                    $black_list[] = $key;
                    $black_list[] = $key . '_name';
                    $black_list[] = $key . '_size';
                }
            }
            $black_list[] = 'attaches';
            $trust = array_diff($schema, $black_list);
        }
        return $trust;
    }

    /**
     * Callback method that listens to the `beforeFind` event in the bound
     * table. It modifies the passed query by eager loading the translated fields
     * and adding a formatter to copy the values into the main table records.
     *
     * @param \Cake\Event\EventInterface $event The beforeFind event that was fired.
     * @param \Cake\ORM\Query $query Query
     * @param \ArrayObject $options The options for the query
     * @return void
     */
    public function beforeFind($event, $query, $options)
    {

        $images = $this->attaches['images'] ?? [];
        $files = $this->attaches['files'] ?? [];
        $fields = $this->getSchema()->columns();

        if ($images || $files) {
            $this->getSchema()->addColumn('attaches', [
                'type' => 'json',
            ]);

            $b = '';
            foreach ($images as $field => $info) {
                $c = '';
                foreach ($info['thumbnails'] as $thumb => $tinfo) {
                    $c .= __(',"{thumb}", CONCAT("{path_image}","{prefix}",{file_name})', [
                        'thumb' => $thumb,
                        'path_image' => DS . UPLOAD_BASE_URL . 'images' . DS . $this->getTable() . DS,
                        'prefix' => $tinfo['prefix'],
                        'file_name' => $field
                    ]);
                }
                $b .= __('{mark}"{field}", IF({file_name} = "" OR {file_name} IS NULL, NULL, JSON_OBJECT("0", CONCAT("{path_image}",{file_name}){c}))', [
                    'mark' => $b != '' ? ',' : '',
                    'field' => $field,
                    'path_image' => DS . UPLOAD_BASE_URL . 'images' . DS . $this->getTable() . DS,
                    'file_name' => $field,
                    'c' => $c
                ]);
            }

            foreach ($files as $field => $file) {
                $b .= __('{mark}"{field}", IF({field} = "" OR {field} IS NULL, NULL, CONCAT("{path_file}",{field}))', [
                    'mark' => $b != '' ? ',' : '',
                    'field' => $field,
                    'path_file' => DS . UPLOAD_BASE_URL . 'files' . DS . $this->getTable() . DS,
                ]);
            }

            $a = __('(JSON_OBJECT({0}))', $b);

            $fields[$this->getAlias() . '__attaches'] = $a;
        }
        // dd($fields);
        return $query->find('all')->select($fields);
    }


    /**
     * Handles the saving of children associations and executing the afterSave logic
     * once the entity for this table has been saved successfully.
     *
     * @param \Cake\Event\EventInterface $event the entity to be saved
     * @param \Cake\Datasource\EntityInterface $entity the entity to be saved
     * @param \ArrayObject $options the options to use for the save operation
     * 
     */
    public function beforeSave($event, $entity, $options)
    {
        //アップロード処理
        if ($entity->has('attaches')) $entity->unset('attaches');
        $this->_uploadAttaches($entity);

        // $this->dispatchEvent('Model.afterSave', compact('event', 'entity', 'options'));
    }


    /**
     * 画像、ファイルアップロード
     * 
     * @param \Cake\Datasource\EntityInterface $entity the entity to be saved
     * */
    public function _uploadAttaches($entity)
    {
        $this->checkUploadDirectory();
        $uuid = Text::uuid();

        $data = $entity->extract($this->getSchema()->columns(), true);
        if ($data) {
            $id = $entity->id ?? '';
            $old_data = $entity->id ? $this->get($entity->id) : null;

            $_att_images = @$this->attaches['images'] ?? [];
            $_att_files = @$this->attaches['files'] ?? [];

            //upload images
            foreach ($_att_images as $columns => $imageConf) {
                $image_name = @$data[$columns];
                if ($image_name && $image_name->getError() === UPLOAD_ERR_OK) {

                    $basedir = $this->path_images;
                    $original_file_name = $image_name->getClientFilename();
                    $tmp_name = $image_name->getStream()->getMetadata('uri');

                    $ext = getExtension($original_file_name);
                    $filepattern = $imageConf['file_name'];
                    //画像 処理方法
                    $convert_method = @$imageConf['method'];
                    if (in_array($ext, $imageConf['extensions'])) {
                        $newname = __('{0}{1}.{2}', $this->getTable(), sprintf($filepattern, $id, $uuid), $ext);
                        // resize images
                        $error = $this->generate_thumbnail(
                            $tmp_name,
                            $basedir . $newname,
                            $imageConf['width'],
                            $imageConf['height'],
                            $convert_method
                        );
                        if ($error == 0) $entity->{$columns} = $newname;

                        //サムネイル
                        if (@$imageConf['thumbnails']) {
                            foreach ($imageConf['thumbnails'] as $suffix => $val) {
                                //画像処理方法
                                $convert_method = @$val['method'];
                                //ファイル名
                                $prefix = @$val['prefix'] ?? $suffix;
                                $_newname = $prefix . $newname;
                                //変換
                                $this->generate_thumbnail(
                                    $tmp_name,
                                    $basedir . $_newname,
                                    $val['width'],
                                    $val['height'],
                                    $convert_method
                                );
                            }
                        }
                        if (!$entity->isNew() && $old_data) {
                            if (isset($old_data->attaches[$columns])) {
                                foreach ($old_data->attaches[$columns] as $image_path) {
                                    if ($image_path && is_file($basedir . $image_path)) {
                                        @unlink($basedir . $image_path);
                                    }
                                }
                                /// remove old thumbnails images
                                if (isset($imageConf['thumbnails'])) {
                                    foreach ($imageConf['thumbnails'] as $suffix => $val) {
                                        $prefix = @$val['prefix'] ?? $suffix;
                                        $_file = $basedir . $prefix . $image_path;
                                        if (is_file($_file)) {
                                            @unlink($_file);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //upload files
            foreach ($_att_files as $columns => $val) {
                $file_name = @$data[$columns];

                if ($file_name && $file_name->getError() === UPLOAD_ERR_OK) {
                    $basedir = $this->path_file;
                    $original_file_name = $file_name->getClientFilename();
                    $tmp_name = $file_name->getStream()->getMetadata('uri');
                    $ext = getExtension($original_file_name);
                    $filepattern = $val['file_name'];

                    if (in_array($ext, $val['extensions'])) {
                        $newname = __('{0}{1}.{2}', $this->getTable(), sprintf($filepattern, $id, $uuid), $ext);

                        @move_uploaded_file($tmp_name, $basedir . $newname);
                        @chmod($basedir . $newname, $this->uploadFileMask);

                        $entity->{$columns} = $newname;
                        $entity->{$columns . '_name'} = $original_file_name;
                        $entity->{$columns . '_size'} = $file_name->getSize();

                        if (!$entity->isNew() && $old_data) {
                            // 旧ファイルの削除
                            if (isset($old_data->{$columns})) {
                                if (is_file($basedir . $old_data->{$columns})) {
                                    @unlink($basedir . $old_data->{$columns});
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * upload以下のフォルダを作成/書き込み権限のチェック
     * 
     * */
    protected function checkUploadDirectory()
    {
        // フォルダを作成するかしないか、$this->uploadDirCreateの値が決める
        new Folder($this->path_images, $this->uploadDirCreate, $this->uploadDirMask);
        new Folder($this->path_file, $this->uploadDirCreate, $this->uploadDirMask);
    }


    /**
     * ファイルアップロード
     * 
     * @param string $size [width]x[height]
     * @param string $dir_tmp アップロード元ファイル(フルパス)
     * @param string $dir_file 変換後のファイルパス（フルパス）
     * @param string $method 処理方法
     *  - fit     $size内に収まるように縮小
     *  - cover   $sizeの短い方に合わせて縮小
     *  - crop    cover 変換後、中心$sizeでトリミング
     * */
    public function convert_img($size, $dir_tmp, $dir_file, $method = 'fit')
    {
        $array_method = ['fit', 'cover', 'crop'];
        list($ow, $oh, $info) = getimagesize($dir_tmp);
        $sz = explode('x', $size);
        $cmdline = $this->convertPath;
        $method = in_array($method, $array_method) ? $method : 'fit';
        $size_default = $ow . 'x' . $oh . '>';

        //サイズ指定ありなら
        if (0 < intval($sz[0]) && 0 < intval($sz[1])) {
            if ($ow <= intval($sz[0]) && $oh <= intval($sz[1])) {
                // pass
            } else {
                //枠をはみ出していれば、縮小
                if ($method === 'cover' || $method === 'crop') {
                    //中央切り取り
                    if (($ow / $oh) <= (intval($sz[0]) / intval($sz[1]))) {
                        //横を基準
                        $size_default = $sz[0] . 'x';
                    } else {
                        //縦を基準
                        $size_default = 'x' . $sz[1];
                    }
                    $size_default .= $size_default . '>';
                    // crop
                    if ($method === 'crop') {
                        $size_default .= ' -gravity center -crop ' . $size . '+0+0';
                    }
                } else {
                    // fit
                    $size_default = $size;
                }
            }
        }

        $option = $this->convertParams . ' ' . $size_default;
        // dd(__('{command_line} -auto-orient {option} {dir_tmp} {dir_file}', ['command_line' => $cmdline, 'option' => $option, 'dir_tmp' => $dir_tmp, 'dir_file' => $dir_file]));
        $a = system(escapeshellcmd(__('{command_line} -auto-orient {option} {dir_tmp} {dir_file}', ['command_line' => $cmdline, 'option' => $option, 'dir_tmp' => $dir_tmp, 'dir_file' => $dir_file])));
        @chmod($dir_file, $this->uploadFileMask);
        return $a;
    }


    /**
     * 
     * @param string $dir_file tmp_name
     * @param string $dir_thumb_file new path file
     * @param integer $max_width with
     * @param integer $max_height height
     * @param float $quality
     */
    public function generate_thumbnail($dir_file, $dir_thumb_file, $max_width, $max_height, $quality = 0.75)
    {
        // The original image must exist
        if (is_file($dir_file)) {
            // Let's create the directory if needed
            $th_path = dirname($dir_thumb_file);
            if (!is_dir($th_path))
                mkdir($th_path, 0777, true);
            // If the thumb does not aleady exists
            if (!is_file($dir_thumb_file)) {
                // Get Image size info
                list($width_orig, $height_orig, $image_type) = @getimagesize($dir_file);
                if (!$width_orig)
                    return 2;
                switch ($image_type) {
                    case 1:
                        $src_im = @imagecreatefromgif($dir_file);
                        break;
                    case 2:
                        $src_im = @imagecreatefromjpeg($dir_file);
                        break;
                    case 3:
                        $src_im = @imagecreatefrompng($dir_file);
                        break;
                }
                if (!$src_im)
                    return 3;


                $aspect_ratio = (float) $height_orig / $width_orig;

                $thumb_height = $max_height;
                $thumb_width = round($thumb_height / $aspect_ratio);
                if ($thumb_width > $max_width) {
                    $thumb_width    = $max_width;
                    $thumb_height   = round($thumb_width * $aspect_ratio);
                }

                $width = intval($thumb_width);
                $height = intval($thumb_height);
                $dst_img = @imagecreatetruecolor($width, $height);
                if (!$dst_img)
                    return 4;
                $success = @imagecopyresampled($dst_img, $src_im, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
                if (!$success)
                    return 4;
                switch ($image_type) {
                    case 1:
                        $success = @imagegif($dst_img, $dir_thumb_file);
                        break;
                    case 2:
                        $success = @imagejpeg($dst_img, $dir_thumb_file, 100);
                        break;
                    case 3:
                        $success = @imagepng($dst_img, $dir_thumb_file, intval($quality * 9));
                        break;
                }
                if (!$success)
                    return 4;
            }
            return 0;
        }
        return 1;
    }


    /**
     * 使っているテーブル：Videos, Channels
     * 存在しないかをチェックするの関数
     * @param string $video_code データベースのCode項目又はYoutube APIから習得できたVideoId・ChannelId
     * @return boolean True/False
     */
    public function check_exists_code($video_code = null)
    {
        return $video_code && $this->get_one_recode_by_code($video_code);
    }

    /**
     * 使っているテーブル：Videos, Channels
     * データベースに繋がって、Codeによってデータを取る
     * @param string $video_code データベースのCode項目又はYoutube APIから習得できたVideoId・ChannelId
     * @return \Cake\Datasource\EntityInterface
     */
    public function get_one_recode_by_code($video_code)
    {
        return $this->findByCode($video_code)->first();
    }

    /**
     * 使っているテーブル：Videos, Channels
     * データベースにデータを保存する
     * @param array $data Youtube APIから習得できたデータ
     * @return \Cake\Datasource\EntityInterface
     */
    public function _save($data)
    {
        if ($this->check_exists_code($data['code'])) {
            $entity = $this->get_one_recode_by_code($data['code']);
            $this->patchEntity($entity, $data);
        } else {
            $entity = $this->newEntity($data);
        }
        return $this->save($entity);
    }
}
