<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ChannelCategory $channel_category
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
                        <li><?= $this->Html->link(__('チャンネルカテゴリー管理一覧'), ['controller' => 'channel-categories', 'action' => 'index']) ?></li>
                        <li><?= $channel_category->isNew() ? '新規登録' : '編集' ?></li>
                    </ul>
                </div>
            </div>
            <div class="filter">
                <div class="column-responsive">
                    <div class="users form content">
                        <?= $this->Form->create($channel_category) ?>
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="8"><?= $channel_category->isNew() ? '新規登録' : '編集' ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="filter-status-label"><?= __('No') ?></th>
                                    <td class="filter-status">
                                        <?= $channel_category->isNew() ? '新規' : sprintf('No. %04d', $channel_category->id) ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="filter-text-short-label"><?= __('チャンネルカテゴリー名') ?></th>
                                    <td class="">
                                        <?= $this->Form->text(
                                            'channel_category_name',
                                            [
                                                'type' => 'text',
                                                'class' => 'wAuto',
                                                'autocomplete' => 'off',
                                                'required' => false,
                                                'maxLength' => 20,
                                            ]
                                        ) ?>
                                        <?php
                                        if ($this->Form->isFieldError('channel_category_name')) {
                                            echo $this->Form->error('channel_category_name');
                                        }
                                        ?>
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        <div class="filter-button">
                            <?php if ($channel_category->isNew()) : ?>
                                <?= $this->Form->button(__('登録する')) ?>
                            <?php else : ?>
                                <?= $this->Form->button(__('変更する')) ?>
                                <?= $this->Html->link('削除する', ['action' => 'delete', $channel_category->id, 'content'], ['class' => 'button button-outline', 'onclick' => "return confirm('データを完全に削除します。よろしいですか？')"]) ?>
                            <?php endif ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>