    </main>
  </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center p-4">
        <i class="bi bi-exclamation-triangle text-warning" style="font-size:2.5rem;"></i>
        <p class="mt-3 mb-0" id="confirmModalMsg">Are you sure?</p>
      </div>
      <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="confirmModalYes" class="btn btn-danger"><i class="bi bi-trash"></i> Yes, Delete</a>
      </div>
    </div>
  </div>
</div>

<script src="<?= SITE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('click', function(e){
  const el = e.target.closest('[data-confirm]');
  if (!el) return;
  e.preventDefault();
  document.getElementById('confirmModalMsg').textContent = el.getAttribute('data-confirm');
  document.getElementById('confirmModalYes').setAttribute('href', el.getAttribute('href'));
  new bootstrap.Modal(document.getElementById('confirmModal')).show();
});
</script>
</body></html>
