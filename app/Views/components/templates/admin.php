<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IT - Insurance Tech">
    <meta name="author" content="Your Company Name">

    <title>IT - Insurance Tech</title>

    <link rel="shortcut icon" href="<?= base_url('img/favicon.png') ?>" type="image/png">
    <link rel="icon" href="<?= base_url('img/favicon.png') ?>" type="image/png">
    <link href="<?= base_url('css/styles.css') ?>" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"
            crossorigin="anonymous"></script>

    <?= $this->renderSection('css') ?>
</head>

<body class="sb-nav-fixed">
<?= $this->include('components/admin/navbar') ?>

<div id="layoutSidenav">
    <?= $this->include('components/admin/sidebar') ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <?php if (session('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session('error') ?>
                    </div>
                <?php endif ?>

                <?= $this->renderSection('content') ?>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Grupo Nobe <?= date('Y') ?></div>
                </div>
            </div>
        </footer>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
<script src="<?= base_url('js/scripts.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
<script src="<?= base_url('js/datatables-simple-demo.js') ?>"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?= $this->renderSection('js') ?>
</body>

</html>