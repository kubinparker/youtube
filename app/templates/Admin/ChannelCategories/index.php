<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ChannelCategory[]|\Cake\Collection\CollectionInterface $channel_categories
 */
$has_page = ($this->Paginator->hasPrev() || $this->Paginator->hasNext());
$key = 0;
$count = is_array($channel_categories) ? (count($channel_categories) - 1) : ($channel_categories->count() - 1);

?>
<div class="users index content">
    <div class="content-title title_area">
        <div class="btn_area">
            <?= $this->Html->link(__('新規登録'), ['action' => 'edit'], ['class' => 'button float-right mar_0 btn_blue']) ?>
        </div>
        <h3 class="pad_r10 mar_0"><?= __('チャンネルカテゴリー管理') ?></h3>
        <div class="pankuzu">
            <ul>
                <li><a href="/admin/" class="icon-icn_home">&nbsp;</a></li>
                <li><span>チャンネルカテゴリー管理一覧</span></li>
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
                                        'channel_category_name',
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
                    <th><?= __('チャンネルカテゴリー名') ?></th>
                    <th class="col_verify"><?= __('確認') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($channel_categories as $category) : ?>
                    <tr id="content-<?= $category->id ?>">

                        <td class="col_no"><?= $this->Number->format($category->id) ?></td>
                        <td><?= $this->Html->link(h(@$category->channel_category_name), ['action' => 'edit', $category->id]); ?></td>

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