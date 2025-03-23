<nav class="sb-topnav navbar navbar-expand navbar-dark" style="background-color: #004583">
    <a class="navbar-brand ps-3" href="#">
        {{ config('app.name') }}
    </a>

    <div class="ms-auto"></div>

    <ul class="navbar-nav me-3">
        <li class="nav-item">
            <livewire:auth.logout-button />
        </li>
    </ul>
</nav>
