<?php

/**
 * @var \App\View\AppView $this
 */
?>
<div class="admins index content">
    <h3><?= __('CakePhp 4') ?></h3>
    <div class="table-responsive">
        <?= $this->Form->create(null, ['url' => [
            'controller' => 'admins',
            'action' => 'index',
            'prefix' => 'Admin',
            'type' => 'post'
        ]]) ?>
        <table>
            <tbody>
                <tr>
                    <td><?= $this->Form->control('login_id', ['type' => 'text', 'label' => false, 'placeholder' => 'Login ID']); ?></td>
                </tr>
                <tr>
                    <td><?= $this->Form->control('password', ['label' => false, 'placeholder' => 'Password']); ?></td>
                </tr>
                <tr>
                    <td>
                        <?= $this->Form->button(__('Login')) ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <?= $this->Form->end() ?>
        <?= $this->Flash->render() ?>
    </div>
</div>