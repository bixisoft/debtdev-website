/* DebtDev interactions */
(function(){
  'use strict';

  // Year in footer
  document.addEventListener('DOMContentLoaded', function(){
    const y = document.getElementById('year');
    if(y){ y.textContent = new Date().getFullYear(); }
  });

  // Bootstrap validation styling
  (function () {
    const forms = document.querySelectorAll('form');
    Array.prototype.slice.call(forms).forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();

  // Mini Calculator logic
  $('#miniCalc').on('submit', function(e){
    e.preventDefault();
    const income = parseFloat($('#income').val() || 0);
    const payments = parseFloat($('#payments').val() || 0);
    const household = $('#household').val();

    if(!income || !payments || !household){
      this.classList.add('was-validated');
      return;
    }

    const dti = income > 0 ? (payments / income) * 100 : 0;
    const dtiFixed = dti.toFixed(1);
    let cls = 'alert-eligible';
    let msg = `<strong>Eligible</strong> — DTI ${dtiFixed}%`;

    if(dti >= 36 && dti <= 45){
      cls = 'alert-review';
      msg = `<strong>Review</strong> — DTI ${dtiFixed}% (borderline)`;
    } else if(dti > 45){
      cls = 'alert-not';
      msg = `<strong>Not Eligible</strong> — DTI ${dtiFixed}%`;
    }

    const $res = $('#calcResult');
    $res.removeClass('d-none alert-eligible alert-review alert-not').addClass('alert ' + cls).html(msg);

    // Prefill audit modal trigger
    $('#emailResult').removeClass('d-none');
    $('#emailResult').off('click').on('click', function(){
      const text = `DTI result: ${dtiFixed}% | Income: ${income} | Payments: ${payments} | Household: ${household}`;
      const modal = document.getElementById('auditModal');
      const message = document.getElementById('message');
      if(message){ message.value = text + "\n\nPlease review and advise."; }
    });
  });

  // Audit form submission (front-end only demo)
  $('#submitAudit').on('click', function(){
    const form = document.getElementById('auditForm');
    if(!form.checkValidity()){
      form.classList.add('was-validated');
      return;
    }
    // Demo: show toast-like alert
    alert('Thank you! Your request has been recorded (demo). We will follow up shortly.');
    const modalEl = document.getElementById('auditModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
    form.reset();
    form.classList.remove('was-validated');
  });

})();