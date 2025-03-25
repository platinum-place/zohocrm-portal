<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <br/>
                <div class="text-center">
                    <img src="<?= base_url('images/logo.png') ?>" alt="Logo IT" width="190" height="170">
                </div>

                <div class="sb-sidenav-menu-heading">&nbsp;</div>
                <a class="nav-link" href="<?= site_url() ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Panel de Control
                </a>

                <div class="sb-sidenav-menu-heading">Cotizaciones</div>

                <a class="nav-link" href="<?= site_url("cotizaciones") ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    Cotizar
                </a>

                <a class="nav-link" href="<?= site_url("cotizaciones/buscar_cotizaciones") ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                    Buscar Cotización
                </a>

                <div class="sb-sidenav-menu-heading">Emisiones</div>

                <a class="nav-link" href="<?= site_url("cotizaciones/buscar_emisiones") ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                    Buscar Emisión
                </a>

                <div class="sb-sidenav-menu-heading">Herramientas</div>

                <a class="nav-link" href="<?= site_url() ?>">
                    Cambiar contraseña
                </a>

                <?php if (session('is_admin')) : ?>
                    <a class="nav-link" href="<?= site_url("cotizaciones/buscar_emisiones") ?>">
                        Generar Zoho token
                    </a>

                    <a class="nav-link" href="<?= site_url("cotizaciones/buscar_emisiones") ?>">
                        Usuarios
                    </a>

                    <a class="nav-link" href="<?= site_url("cotizaciones/buscar_emisiones") ?>">
                        Empresas
                    </a>

                    <a class="nav-link" href="<?= site_url("cotizaciones/buscar_emisiones") ?>">
                        Asignar empresas
                    </a>
                <?php endif ?>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Ingresaste como:</div>
            <?= session('name') ?>
        </div>
    </nav>
</div>