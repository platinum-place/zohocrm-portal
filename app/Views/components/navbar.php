<nav class="sb-topnav navbar navbar-expand navbar-dark" style="background-color: #004583">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="#"><?= session('company_name') ?></a>
    <!-- Navbar Search-->
    <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        &nbsp;
    </div>
    <!-- Navbar-->
    <form method="POST" action="<?= site_url('login') ?>" class="ms-auto ms-md-0 me-3 me-lg-4">
        <?= csrf_field() ?>

        <input type="hidden" name="_method" value="PUT">

        <button type="submit" class="btn btn-link p-0">
            <i class="fas fa-sign-out-alt fa-fw"></i>
        </button>
    </form>
</nav>