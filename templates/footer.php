<?php
require_once __DIR__ . '/../includes/csrf.php';
$csrfToken = isset($_SESSION['user_id']) ? csrf_token() : '';
?>
<?php if (isset($_SESSION['user_id'])): ?>
<div id="remove-backdrop" class="remove-backdrop" hidden>
    <div class="remove-dialog" role="dialog" aria-modal="true" aria-labelledby="remove-dialog-title">
        <h2 id="remove-dialog-title">Remove this movie?</h2>
        <form id="remove-form" method="POST" action="">
            <input type="hidden" name="id" id="remove-id" value="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="remove-actions">
                <button type="button" class="btn-flat white-text" id="remove-cancel">Cancel</button>
                <button type="submit" class="btn red" id="remove-confirm">Remove</button>
            </div>
        </form>
    </div>
</div>
<style>
    .remove-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .remove-backdrop[hidden] {
        display: none !important;
    }
    .remove-dialog {
        background: #1e1e1e;
        color: #fff;
        border-radius: 8px;
        padding: 24px;
        width: 100%;
        max-width: 360px;
    }
    .remove-dialog h2 {
        margin: 0 0 20px;
        font-size: 1.4rem;
    }
    .remove-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
</style>
<script>
    (function () {
        var backdrop = document.getElementById("remove-backdrop");
        var title = document.getElementById("remove-dialog-title");
        var form = document.getElementById("remove-form");
        var idInput = document.getElementById("remove-id");
        var cancel = document.getElementById("remove-cancel");
        if (!backdrop || !form) return;

        function closeDialog() {
            backdrop.hidden = true;
            form.action = "";
            idInput.value = "";
        }

        function openDialog(button) {
            title.textContent = button.getAttribute("data-title") || "Remove this movie?";
            form.action = button.getAttribute("data-action") || "";
            idInput.value = button.getAttribute("data-id") || "";
            backdrop.hidden = false;
            cancel.focus();
        }

        document.addEventListener("click", function (event) {
            var button = event.target.closest("[data-remove]");
            if (button) {
                openDialog(button);
                return;
            }
            if (event.target === backdrop) closeDialog();
        });
        cancel.addEventListener("click", closeDialog);
        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape" && !backdrop.hidden) closeDialog();
        });
    })();
</script>
<?php endif; ?>
</main>
<footer class="page-footer #1f1f1f grey darken-4" style="margin-top: 40px;">
    <div class="footer-copyright">
        <div class="container center">
            &copy; 2026 Cinema Management System — Admin Portal
        </div>
    </div>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>

</html>