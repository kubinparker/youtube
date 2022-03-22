<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VideoCategory $video_category
 */
?>

<div class="row">
    <div class="column-responsive">
        <div class="users form content">
            <div class="content-title title_area">
                <h3 class="pad_r10 mar_0"><?= __('カテゴリー管理') ?></h3>
                <div class="pankuzu">
                    <ul>
                        <li>
                            <?= $this->Html->link(html_entity_decode('&nbsp;'), ['controller' => 'admins', 'action' => 'index'], ['class' => 'icon-icn_home']) ?>
                        </li>
                        <li><?= $this->Html->link(__('動画カテゴリー管理一覧'), ['controller' => 'video-categories', 'action' => 'index']) ?></li>
                        <li><?= $video_category->isNew() ? '新規登録' : '編集' ?></li>
                    </ul>
                </div>
            </div>
            <div class="filter">
                <div class="column-responsive">
                    <div class="users form content">
                        <?= $this->Form->create($video_category) ?>
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="8"><?= $video_category->isNew() ? '新規登録' : '編集' ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="filter-status-label"><?= __('No') ?></th>
                                    <td class="filter-status">
                                        <?= $video_category->isNew() ? '新規' : sprintf('No. %04d', $video_category->id) ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="filter-text-short-label"><?= __('動画カテゴリー名') ?></th>
                                    <td class="">
                                        <?= $this->Form->text(
                                            'video_catergory_name',
                                            [
                                                'type' => 'text',
                                                'class' => 'wAuto',
                                                'autocomplete' => 'off',
                                                'required' => false,
                                                'maxLength' => 20,
                                            ]
                                        ) ?>
                                        <?php
                                        if ($this->Form->isFieldError('video_catergory_name')) {
                                            echo $this->Form->error('video_catergory_name');
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="filter-status-label"><?= __('親カテゴリ') ?></th>
                                    <td class="filter-status">
                                        <?php unset($parent_categories[$video_category->id]) ?>
                                        <?= $this->Form->select(
                                            'category_parent_id',
                                            $parent_categories,
                                            [
                                                'empty' => [0 => '-- 親カテゴリ -- '],
                                                'class' => 'wUnset'
                                            ]
                                        ) ?>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        <?= $this->Flash->render() ?>
                        <div class="filter-button">
                            <?php if ($video_category->isNew()) : ?>
                                <?= $this->Form->button(__('登録する')) ?>
                            <?php else : ?>
                                <?= $this->Form->button(__('変更する')) ?>
                                <?= $this->Html->link('削除する', ['action' => 'delete', $video_category->id, 'content'], ['class' => 'button button-outline', 'onclick' => "return confirm('データを完全に削除します。よろしいですか？')"]) ?>
                            <?php endif ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>