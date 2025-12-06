<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Tabel List Pengguna</h4>
        </div>
        <div class="card-content mb-4 px-4">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahUser">
                <i class="fa fa-plus"></i> Tambah Pengguna Baru
            </button>
            <div class="table-responsive">
                <br>
                <table class="table table-bordered table-striped table-hover mb-0 text-center" id="userTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Peran</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomor = 1; foreach ($users as $row): ?>
                        <tr>
                            <td><?= $nomor++; ?></td>
                            <td><?= $row['username']; ?></td>
                            <td><?= $row['nama']; ?></td>
                            <td><?= $row['email']; ?></td>
                            <td><?= $row['telepon']; ?></td>
                            <td><?= $row['peran']; ?></td>
                            <td>
                                <a href="#" data-id="<?= $row['id_user']; ?>" class="btn btn-info text-white edit-btn">
                                    <i class="fa fa-pen"></i> Edit
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </div>
</div>

<?= view('pages/admin/users/modal') ?> <!-- This will include the modal from modal_pengeluaran.php -->

<?= $this->endSection() ?> <!-- End content section -->