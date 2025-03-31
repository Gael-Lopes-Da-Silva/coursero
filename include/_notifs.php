<?php if (isset($_SESSION['notification'])): ?>
    <div class="position-absolute bottom-0 end-0 p-2">
        <div class="toast align-items-center text-bg-<?= $_SESSION['notification']['type'] ?>" id="toastNotification" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"><?= $_SESSION['notification']['message'] ?></div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var toastEl = document.getElementById("toastNotification");
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    </script>

    <?php unset($_SESSION['notification']) ?>
<?php endif; ?>
