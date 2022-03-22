<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
$has_page = ($this->Paginator->hasPrev() || $this->Paginator->hasNext());
$key = 0;
$count = $users->count() - 1;
?>
<div class="users index content">
    <div class="content-title title_area">
        <div class="btn_area">
            <?= $this->Html->link(__('新規登録'), ['action' => 'edit'], ['class' => 'button float-right mar_0 btn_blue']) ?>
        </div>
        <h3 class="pad_r10 mar_0"><?= __('ユーザー管理') ?></h3>
        <div class="pankuzu">
            <ul>
                <li><a href="/admin/" class="icon-icn_home">&nbsp;</a></li>
                <li><span>ユーザー管理一覧</span></li>
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
                                <th class="filter-text-short-label"><?= __('Login ID') ?></th>
                                <td class="">
                                    <?= $this->Form->text(
                                        'login_id',
                                    ) ?>
                                </td>
                                <th class="filter-text-short-label"><?= __('氏名') ?></th>
                                <td class="">
                                    <?= $this->Form->text(
                                        'full_name',
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
                    <th style="width:10em"><?= __('Login ID') ?></th>
                    <th><?= __('氏名') ?></th>
                    <th style="width:20em"><?= __('メールアドレス') ?></th>
                    <th class="col_date"><?= __('メール送信') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr id="content-<?= $user->id ?>">
                        <td class="col_no"><?= $this->Number->format($user->id) ?></td>
                        <td style="width:10em"><?= h($user->login_id) ?></td>
                        <td><?= $this->Html->link(h(@$user->full_name), ['action' => 'edit', $user->id]); ?></td>
                        <td style="width:20em"><?= h($user->email) ?></td>
                        <td class="col_date">
                            <?= $this->Form->create(null, ['type' => 'post']) ?>
                            <?= $this->Form->hidden('id', ['value' => $user->id]) ?>
                            <?= $this->Form->button(__('送信')) ?>
                            <?= $this->Form->end() ?>
                        </td>
                    </tr>
                    <?php $key++ ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $this->element('paginator', ['class' => 'admin', 'has_page' => $has_page]) ?>
</div>