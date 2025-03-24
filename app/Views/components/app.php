<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <title>IT - Insurance Tech</title>
    <link rel="icon" type="image/png" href="<?= base_url('images/favicon.ico') ?>">
    <link href="<?= base_url('css/styles.css') ?>" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <?= $this->renderSection('css') ?>
</head>

<body>

<?= $this->include('components/navbar') ?>

<div id="layoutSidenav">
    <?= $this->include('components/sidebar') ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>
</div>

<?= $this->renderSection('js') ?>
</body>

</html>