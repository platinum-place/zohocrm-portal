<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <br />
                <div class="text-center">
                    <img src="<?= base_url('images/logo.png') ?>" alt="Logo IT" width="200" height="190">
                </div>

                <div class="sb-sidenav-menu-heading">&nbsp;</div>

                <a class="nav-link <?= strpos(current_url(), 'users') !== false ? 'active' : '' ?>" href="<?= site_url('admin/users') ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Usuarios
                </a>

                <a class="nav-link <?= strpos(current_url(), 'clients') !== false ? 'active' : '' ?>" href="<?= site_url('admin/clients') ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Clientes
                </a>

<!--                <a class="nav-link" href="--><?php //= site_url() ?><!--">-->
<!--                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>-->
<!--                    Panel de Control-->
<!--                </a>-->
<!---->
<!--                <div class="sb-sidenav-menu-heading">Cotizaciones</div>-->
<!---->
<!--                <a class="nav-link" href="--><?php //= site_url("cotizaciones") ?><!--">-->
<!--                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>-->
<!--                    Cotizar-->
<!--                </a>-->
<!---->
<!--                <a class="nav-link" href="--><?php //= site_url("cotizaciones/buscar_cotizaciones") ?><!--">-->
<!--                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>-->
<!--                    Buscar Cotización-->
<!--                </a>-->
<!---->
<!--                <div class="sb-sidenav-menu-heading">Emisiones</div>-->
<!---->
<!--                <a class="nav-link" href="--><?php //= site_url("cotizaciones/buscar_emisiones") ?><!--">-->
<!--                    <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>-->
<!--                    Buscar Emisión-->
<!--                </a>-->

                <!-- <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#generar_reporte">
                    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                    Generar Reporte
                </a> -->
            </div>
        </div>
<!--        <div class="sb-sidenav-footer">-->
<!--            <div class="small">Ingresaste como:</div>-->
<!--            --><?php //= session('usuario') ?>
<!--        </div>-->
    </nav>
</div>