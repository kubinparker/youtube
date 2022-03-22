<?php if ($has_page) : ?>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<<') ?>
            <?= $this->Paginator->hasPrev() ? $this->Paginator->prev('<') : '' ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->hasNext() ? $this->Paginator->next('>') : '' ?>
            <?= $this->Paginator->last('>>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('{{count}} 件中  {{start}}〜{{end}} 件表示（{{page}}/{{pages}}ページ）')); ?></p>
    </div>
<?php endif; ?>