<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<div class="row">
    <div class="column-responsive">
        <div class="users form content">
            <div class="content-title title_area">
                <h3 class="pad_r10 mar_0"><?= __('ユーザー管理') ?></h3>
                <div class="pankuzu">
                    <ul>
                        <li>
                            <?= $this->Html->link(html_entity_decode('&nbsp;'), ['controller' => 'admins', 'action' => 'index'], ['class' => 'icon-icn_home']) ?>
                        </li>
                        <li><?= $this->Html->link(__('ユーザー管理一覧'), ['controller' => 'users', 'action' => 'index']) ?></li>
                        <li><?= $user->isNew() ? '新規登録' : '編集' ?></li>
                    </ul>
                </div>
            </div>
            <div class="filter">
                <div class="column-responsive">
                    <div class="users form content">
                        <?= $this->Form->create($user) ?>
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="8"><?= $user->isNew() ? '新規登録' : '編集' ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="filter-status-label"><?= __('No') ?></th>
                                    <td class="filter-status">
                                        <?= $user->isNew() ? '新規' : sprintf('No. %04d', $user->id) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="filter-text-short-label"><?= __('Login ID') ?></th>
                                    <td class="">
                                        <?= $this->Form->text(
                                            'login_id',
                                            [
                                                'class' => 'wAuto',
                                                'autocomplete' => 'off',
                                                'required' => false,
                                                'maxLength' => 20
                                            ]
                                        ) ?>
                                        <?php
                                        if ($this->Form->isFieldError('login_id')) {
                                            echo $this->Form->error('login_id');
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="filter-text-short-label"><?= __('パスワード') ?></th>
                                    <td class="">
                                        <?= $this->Form->text(
                                            'password',
                                            [
                                                'type' => 'password',
                                                'class' => 'wAuto',
                                                'autocomplete' => 'off',
                                                'required' => false,
                                                'maxLength' => 20,
                                                'value' => ''
                                            ]
                                        ) ?>
                                        <?php
                                        if ($this->Form->isFieldError('password')) {
                                            echo $this->Form->error('password');
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="filter-text-short-label"><?= __('氏名') ?></th>
                                    <td class="">
                                        <?= $this->Form->text(
                                            'full_name',
                                            [
                                                'class' => 'wAuto',
                                                'autocomplete' => 'off',
                                                'required' => false,
                                                'maxLength' => 20
                                            ]
                                        ) ?>
                                        <?php
                                        if ($this->Form->isFieldError('full_name')) {
                                            echo $this->Form->error('full_name');
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="filter-text-short-label"><?= __('メールアドレス') ?></th>
                                    <td class="">
                                        <?= $this->Form->text(
                                            'email',
                                            [
                                                'class' => 'wAuto',
                                                'autocomplete' => 'off',
                                                'required' => false,
                                                'maxLength' => 200
                                            ]
                                        ) ?>
                                        <?php
                                        if ($this->Form->isFieldError('email')) {
                                            echo $this->Form->error('email');
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="filter-button">
                            <?php if ($user->isNew()) : ?>
                                <?= $this->Form->button(__('登録する')) ?>
                            <?php else : ?>
                                <?= $this->Form->button(__('変更する')) ?>
                                <?= $this->Html->link('削除する', ['action' => 'delete', $user->id, 'content'], ['class' => 'button button-outline', 'onclick' => "return confirm('データを完全に削除します。よろしいですか？')"]) ?>
                            <?php endif ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>