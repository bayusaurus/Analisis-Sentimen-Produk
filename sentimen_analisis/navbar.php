<nav class="navbar navbar-expand-sm navbar-dark bg-dark" data-toggle="affix" style="padding-top: 20px; padding-bottom: 20px;">
    <div class="mx-auto d-sm-flex d-block flex-sm-nowrap">
        <a class="navbar-brand" href="index.php">Sentiment Analyzer</a>
        <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#navbarsExample11" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse " id="navbarsExample11">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="data_latih.php">Data Latih</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="fitur.php">Fitur Data Latih</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="klasifikasi.php">Pengujian dan Pelabelan</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="admin.php">Admin</a>
                </li>
                <li class="nav-item dropdown active">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $_SESSION['nama']; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalLogout">Log out</a>
                </div>
            </li>
        </ul>
    </div>
</div>
</nav>