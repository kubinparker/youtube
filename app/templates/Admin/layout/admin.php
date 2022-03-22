<!DOCTYPE html>
<html>

<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cake4</title>
    <?= $this->fetch('meta') ?>
    <?= $this->Html->meta('icon') ?>
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
    <?= $this->Html->css(['normalize.min', 'font', 'milligram.min', 'cake', 'common', 'admin/common']) ?>
    <?= $this->fetch('css') ?>

    <?= $this->Html->script(['jquery-1.11.2.min', 'libs', 'admin/base', 'admin/cms_index']); ?>
    <?= $this->fetch('script') ?>
    <?= $this->fetch('beforeHeaderClose'); ?>
</head>

<body>
    <?= $this->fetch('afterBodyStart'); ?>
    <?= $this->element('header'); ?>
    <?= $this->element('side'); ?>
    <main id="content" class="main">
        <?= $this->fetch('beforeContentStart'); ?>
        <div class="container">
            <?= $this->fetch('content') ?>
        </div>
        <?= $this->fetch('afterContentClose') ?>
    </main>
    <footer>
        <?= $this->element('footer'); ?>
    </footer>
    <?= $this->fetch('beforeBodyClose'); ?>

    <script type="text/javascript">
        $(function() {
            var re = document.getElementById('clock');
            var item = function() {
                var items = new Date();
                h = ('0' + items.getHours()).slice(-2);
                m = ('0' + items.getMinutes()).slice(-2);
                s = ('0' + items.getSeconds()).slice(-2);
                re.innerHTML = h + ':' + m;
                setTimeout(item, 100);
            }
            setTimeout(item, 100);
        });
    </script>
</body>

</html>