<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VideoCategory[]|\Cake\Collection\CollectionInterface $video_categories
 */
$has_page = ($this->Paginator->hasPrev() || $this->Paginator->hasNext());
$key = 0;

$count = is_array($video_categories) ? (count($video_categories) - 1) : ($video_categories->count() - 1);

?>
<div class="users index content">
    <div class="content-title title_area">
        <div class="btn_area">
            <?= $this->Html->link(__('新規登録'), ['action' => 'edit'], ['class' => 'button float-right mar_0 btn_blue']) ?>
        </div>
        <h3 class="pad_r10 mar_0"><?= __('動画カテゴリー管理') ?></h3>
        <div class="pankuzu">
            <ul>
                <li><a href="/admin/" class="icon-icn_home">&nbsp;</a></li>
                <li><span>動画カテゴリー管理一覧</span></li>
            </ul>
        </div>
    </div>
    <div class="table-responsive <?= $has_page ? "has_paginator" : "" ?>">
        <div class="filter">
            <div class="column-responsive column-80">
                <div class="users form content">
                    <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => 'query']) ?>
                    <table>
                        <thead>
                            <tr>
                                <th colspan="8">絞り込み</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th class="filter-text-short-label"><?= __('チャンネルカテゴリー名') ?></th>
                                <td class="">
                                    <?= $this->Form->text(
                                        'video_catergory_name',
                                    ) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="filter-button">
                        <?= $this->Form->button(__('検索')) ?>
                        <?= $this->Html->link('リセット', ['action' => 'index'], ['class' => 'button button-outline']) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
        <table class=" list-data">
            <thead>
                <tr>
                    <th class="col_no"><?= __('No') ?></th>
                    <th><?= __('カテゴリー名') ?></th>
                    <th class="col_verify"><?= __('確認') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($video_categories as $id => $category_video) : ?>
                    <?php $key++ ?>
                    <tr>
                        <td class="col_no"><?= $this->Number->format($key) ?></td>
                        <td><?= $this->Html->link(h($category_video), ['action' => 'edit', $id]); ?></td>
                        <td class="col_verify">
                            <div class="prev">
                                <?= $this->Html->link('プレビュー', ['prefix' => false, 'action' => 'index', $id, "?" => ['preview' => 'on']], ['target' => '_blank']); ?>
                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $this->element('paginator', ['class' => 'admin', 'has_page' => $has_page]) ?>
</div>