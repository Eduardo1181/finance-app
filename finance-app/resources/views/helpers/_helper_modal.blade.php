<div class="modal fade" id="info-modal" role="dialog" data-bs-keyboard="false" data-bs-backdrop="static">
  <div class="modal-dialog" role="document" id="modal-dialog-custom">
    <div class="modal-content">
      <button
        type="button"
        class="btn-close"
        data-bs-dismiss="modal"
        aria-label="Fechar"
        style="position: absolute; top: 15px; right: 15px; z-index: 10;"
      ></button>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<style>
  .modal-full {
    width: 95% !important;
    max-width: 95% !important;
  }
</style>

<script>
  window.initSelect2 = function () {
    const select = document.querySelector('#categorySelect');
    if (select && !$(select).hasClass("select2-hidden-accessible")) {
      $(select).select2({
        tags: true,
        placeholder: "Escolha ou crie uma categoria",
        language: "pt-BR",
        width: '100%'
      });
    }
  };

  ModalHelperConf = {
    close: function () {
      const modal = bootstrap.Modal.getInstance(document.getElementById('info-modal'));
      if (modal) modal.hide();
    },

    changeStyle: function () {
      let footers = document.getElementsByClassName('modal-footer');
      let closes = document.getElementsByClassName('btn-close');
      if (footers.length) footers[footers.length - 1].style.display = "none";
      if (closes.length) closes[closes.length - 1].style.display = "none";
    },

    revokeStyle: function () {
      let footers = document.getElementsByClassName('modal-footer');
      let closes = document.getElementsByClassName('btn-close');
      if (footers.length) footers[footers.length - 1].style.display = "";
      if (closes.length) closes[closes.length - 1].style.display = "";
    },

    modalDialog: $('#modal-dialog-custom'),
    modalBody: $('#info-modal .modal-body'),

    sizeOfModal: function (size) {
      this.modalDialog.removeClass('modal-lg modal-full');
      this.modalDialog.css({ 'width': '', 'max-width': '' });

      switch (size) {
        case 'default':
          break;
        case 'lg':
          this.modalDialog.addClass('modal-lg');
          break;
        case 'full':
          this.modalDialog.addClass('modal-full');
          break;
      }
    },

    retrieve: function (url) {
      $.ajax({
        url: url,
        method: 'get',
        beforeSend: function () {
          ModalHelperConf.modalBody.html(
            `<div class="text-center fa-2x p-4">
              <i class="fa fa-spinner fa-pulse"></i> Carregando...
            </div>`
          );
        },
        success: function (response) {
          ModalHelperConf.modalBody.html(response);

          if (typeof initSelect2 === 'function') {
            initSelect2();
          }
        },
        error: function () {
          ModalHelperConf.modalBody.html(`
            <h4 class="modal-title">Ops!</h4>
            <hr>
            <div class="alert alert-danger">Falha no carregamento!</div>
          `);
        }
      });
    },

    callbacks: {},

    load: function (url, size, autoClose = true, callbacks = null) {
      this.sizeOfModal(size);
      $('#info-modal').attr('data-bs-backdrop', autoClose ? 'true' : 'static');

      let modalInstance = new bootstrap.Modal(document.getElementById('info-modal'));
      modalInstance.show();

      this.retrieve(url);

      if (callbacks) {
        for (const cb of Object.keys(callbacks)) {
          this.callbacks[cb] = callbacks[cb];
        }
      }
    }
  };

  ModalHelper = {
    loadFullModal: function (url, autoClose = true, callbacks = null) {
      ModalHelperConf.load(url, 'full', autoClose, callbacks);
    },
    loadLargeModal: function (url, autoClose = true, callbacks = null) {
      ModalHelperConf.load(url, 'lg', autoClose, callbacks);
    },
    loadModal: function (url, autoClose = true, callbacks = null) {
      ModalHelperConf.load(url, 'default', autoClose, callbacks);
    },
    exec: function (callback) {
      if (ModalHelperConf.callbacks[callback]) {
        ModalHelperConf.callbacks[callback]();
      }
    }
  };
</script>

