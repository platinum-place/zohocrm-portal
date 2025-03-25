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
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"
            crossorigin="anonymous"></script>
    <?= $this->renderSection('css') ?>
</head>

<body class="sb-nav-fixed">

<?= $this->include('components/navbar') ?>

<div id="layoutSidenav">
    <?= $this->include('components/sidebar') ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; <?= date('Y') ?></div>
                </div>
            </div>
        </footer>
    </div>
</div>

<?= $this->renderSection('content') ?>

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