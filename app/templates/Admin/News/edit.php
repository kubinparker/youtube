<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\News $news
 */
?>




<div class="row">
    <div class="column-responsive">
        <div class="users form content">
            <div class="content-title title_area">
                <h3 class="pad_r10 mar_0"><?= __('広告管理') ?></h3>
                <div class="pankuzu">
                    <ul>
                        <li>
                            <?= $this->Html->link(html_entity_decode('&nbsp;'), ['controller' => 'admins', 'action' => 'index'], ['class' => 'icon-icn_home']) ?>
                        </li>
                        <li><?= $this->Html->link(__('広告管理一覧'), ['controller' => 'users', 'action' => 'index']) ?></li>
                        <li><?= $news->isNew() ? '新規登録' : '編集' ?></li>
                    </ul>
                </div>
            </div>
            <div class="filter">
                <div class="column-responsive">
                    <div class="users form content">
                        <?= $this->Form->create($news, ['type' => 'file']) ?>
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="8"><?= $news->isNew() ? '新規登録' : '編集' ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="filter-status-label"><?= __('No') ?></th>
                                    <td class="filter-status">
                                        <?= $news->isNew() ? '新規' : sprintf('No. %04d', $news->id) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="filter-text-short-label"><?= __('タイトル') ?></th>
                                    <td class="">
                                        <?= $this->Form->text(
                                            'title',
                                            [
                                                'class' => 'w50per',
                                                'autocomplete' => 'off',
                                                'required' => false,
                                                'maxLength' => 20,
                                            ]
                                        ) ?>
                                        <?php
                                        if ($this->Form->isFieldError('title')) {
                                            echo $this->Form->error('title');
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="filter-text-short-label"><?= __('公開日') ?></th>
                                    <td class="">
                                        <?= $this->form->dateTime(
                                            'published_at',
                                            [
                                                'value' => new \DateTime('now', new \DateTimeZone('Asia/Tokyo')),
                                                'class' => 'wAuto',
                                                'autocomplete' => 'off',
                                                'required' => false,
                                            ]
                                        ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="filter-text-short-label"><?= __('画像') ?></th>
                                    <td class="">
                                        <?= $this->Form->text(
                                            'image',
                                            [
                                                'type' => 'file',
                                                'class' => 'wAuto',
                                                'autocomplete' => 'off',
                                                'required' => false,
                                            ]
                                        ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="filter-text-short-label"><?= __('コンテンツ') ?></th>
                                    <td class="">
                                        <?= $this->Form->text(
                                            'content',
                                            [
                                                'type' => 'textarea',
                                                'class' => 'w100per',
                                                'autocomplete' => 'off',
                                                'required' => false,
                                            ]
                                        ) ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="filter-button">
                            <?php if ($news->isNew()) : ?>
                                <?= $this->Form->button(__('登録する')) ?>
                            <?php else : ?>
                                <?= $this->Form->button(__('変更する')) ?>
                                <?= $this->Html->link('削除する', ['action' => 'delete', $news->id, 'content'], ['class' => 'button button-outline', 'onclick' => "return confirm('データを完全に削除します。よろしいですか？')"]) ?>
                            <?php endif ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>