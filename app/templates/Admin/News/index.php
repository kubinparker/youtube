<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\News[]|\Cake\Collection\CollectionInterface $news
 */
$has_page = ($this->Paginator->hasPrev() || $this->Paginator->hasNext());
$key = 0;
$count = $news->count() - 1;
?>
<div class="index content">
    <div class="content-title title_area">
        <div class="btn_area">
            <?= $this->Html->link(__('新規登録'), ['action' => 'edit'], ['class' => 'button float-right mar_0 btn_blue']) ?>
        </div>
        <h3 class="pad_r10 mar_0"><?= __('広告管理') ?></h3>
        <div class="pankuzu">
            <ul>
                <li><a href="/admin/" class="icon-icn_home">&nbsp;</a></li>
                <li><span>広告管理一覧</span></li>
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
                                <th class="filter-text-short-label"><?= __('タイトル') ?></th>
                                <td class="">
                                    <?= $this->Form->text(
                                        'title',
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
                    <th style="width:10em"><?= __('公開日') ?></th>
                    <th><?= __('タイトル') ?></th>
                    <th style="width:20em"><?= __('画像') ?></th>
                    <th class="col_verify"><?= __('確認') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($news as $new) : ?>
                    <tr id="content-<?= $new->id ?>">
                        <td class="col_no"><?= $this->Number->format($new->id) ?></td>
                        <td style="width:10em"><?= $new->published_at ?></td>
                        <td><?= $this->Html->link(h(@$new->title), ['action' => 'edit', $new->id]); ?></td>
                        <td style="width:20em"><?= h($new->image) ?></td>
                        <td class="col_verify">
                            <div class="prev">
                                <?= $this->Html->link('プレビュー', ['prefix' => false, 'action' => 'index', $category->id, "?" => ['preview' => 'on']], ['target' => '_blank']); ?>
                            </div>
                        </td>
                    </tr>
                    <?php $key++ ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $this->element('paginator', ['class' => 'admin', 'has_page' => $has_page]) ?>
</div>